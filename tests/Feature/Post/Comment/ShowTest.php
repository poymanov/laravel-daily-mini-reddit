<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Отображение страницы просмотра комментария
     */
    public function testShowScreenCanBeRendered()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertOk();
    }

    /**
     * Отображение данных по комментарию
     */
    public function testDataCanBeRendered()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertSee($comment->text);
        $response->assertSee('Author: ' . $comment->user->name);
        $response->assertSee('Created: ' . $comment->created_at->diffForHumans());
    }

    /**
     * Попытка просмотр комментария для публикации, которая не относится к указанному сообществу
     */
    public function testWrongCommunityPostCombination()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost();
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра комментария, который не относится к указанному комментарию
     */
    public function testWrongPostCommentCombination()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment();

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра несуществующего комментария
     */
    public function testNotExistedComment()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, 999));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра удаленного комментария
     */
    public function testDeletedComment()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id], true);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Отображение данных по публикации, к которой относится комментарий
     */
    public function testPostInfo()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));
        $response->assertSee($post->title);
        $response->assertSee($this->buildPostShowUrl($community->slug, $post->slug));
    }

    /**
     * Кнопка удаления не отображается для гостей
     */
    public function testDeleteButtonForGuest()
    {
        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertDontSee('Delete Comment');
        $response->assertDontSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка удаления не отображается для пользователей с неподтвержденным email
     */
    public function testDeleteButtonForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertDontSee('Delete Comment');
        $response->assertDontSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка удаления не отображается для комментария другого пользователя
     */
    public function testDeleteButtonForNotOwner()
    {
        $this->signIn();

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertDontSee('Edit Comment');
        $response->assertDontSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка удаления не отображается для комментария, который был создан более суток назад
     */
    public function testDeleteButtonExpired()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertDontSee('Delete Comment');
        $response->assertDontSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Отображение кнопки удаления
     */
    public function testDeleteButtonSuccess()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertSee('Delete Comment');
        $response->assertSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Отображение кнопки удаления для администратора
     */
    public function testDeleteButtonSuccessAdmin()
    {
        $this->signIn($this->createAdmin());

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertSee('Delete Comment');
        $response->assertSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Отображение кнопки удаления для администратора, даже для комментария, созданного более суток назад
     */
    public function testDeleteButtonExpiredSuccessAdmin()
    {
        $this->signIn($this->createAdmin());

        $community = $this->createCommunity();
        $post = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id, 'created_at' => now()->subDays(2)]);
        $response = $this->get($this->buildShowUrl($community->slug, $post->slug, $comment->id));

        $response->assertSee('Delete Comment');
        $response->assertSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Формирование пути страницы просмотра публикации
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildPostShowUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug;
    }

    /**
     * Формирование пути страницы просмотра комментария
     *
     * @param string $communitySlug
     * @param string $postSlug
     * @param int    $commentId
     *
     * @return string
     */
    protected function buildShowUrl(string $communitySlug, string $postSlug, int $commentId): string
    {
        return $this->buildPostShowUrl($communitySlug, $postSlug) . '/comments/' . $commentId;
    }

    /**
     * Формирование пути для удаления комментария
     *
     * @param string $communitySlug
     * @param string $postSlug
     * @param int $commentId
     *
     * @return string
     */
    protected function buildDeleteUrl(string $communitySlug, string $postSlug, int $commentId): string
    {
        return $this->buildPostShowUrl($communitySlug, $postSlug) . '/comments/' . $commentId;
    }
}
