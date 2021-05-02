<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Форма редактирования недоступна для гостей
     */
    public function testCannotBeRenderedForGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * ФФорма редактирования недоступна для пользователей с неподтвержденным email
     */
    public function testCannotBeRenderedForNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Попытка просмотра формы редактирования для комментария другого пользователя
     */
    public function testCannotBeRenderedForNotOwner()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn();
        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertForbidden();
    }

    /**
     * Попытка просмотра формы редактирования, если публикация не отновится к сообществу
     */
    public function testCannotBeRenderedForWrongCommunityPostCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost();
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра формы редактирования, если комментарий не относится к публикации
     */
    public function testCannotBeRenderedForWrongPostCommentCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['user_id' => $user->id]);

        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра формы редактирования для комментария, который был создан более суток назад
     */
    public function testCannotBeRenderedForExpiredComment()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);

        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertForbidden();
    }

    /**
     * Успешное отображение формы редактирования комментария
     */
    public function testSuccessForm()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->get($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
        $response->assertOk();

        $response->assertSee('Back to ' . $post->title);
        $response->assertSee('Edit Comment');
        $response->assertSee($comment->text);
        $response->assertSee('Update');
    }

    /**
     * Кнопка редактирования не отображается для гостей
     */
    public function testEditButtonForGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit Comment');
        $response->assertDontSee($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка редактирования не отображается для пользователей с неподтвержденным email
     */
    public function testEditButtonForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit Comment');
        $response->assertDontSee($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка редактирования не отображается для комментария другого пользователя
     */
    public function testEditButtonForNotOwner()
    {
        $this->signIn();

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit Comment');
        $response->assertDontSee($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Кнопка редактирования не отображается для комментария, который был создан более суток назад
     */
    public function testEditButtonExpired()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit Comment');
        $response->assertDontSee($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Отображение кнопки редактирования
     */
    public function testEditButtonSuccess()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertSee('Edit Comment');
        $response->assertSee($this->buildEditPostCommentUrl($community->slug, $post->slug, $comment->id));
    }

    /**
     * Формирование пути для просмотра публикации
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
     * Формирование пути для редактирования комментария
     *
     * @param string $communitySlug
     * @param string $postSlug
     * @param int    $commentId
     *
     * @return string
     */
    protected function buildEditPostCommentUrl(string $communitySlug, string $postSlug, int $commentId): string
    {
        return $this->buildPostShowUrl($communitySlug, $postSlug) . '/comments/' . $commentId . '/edit';
    }
}
