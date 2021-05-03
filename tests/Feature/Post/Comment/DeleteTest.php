<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Попытка удаления гостем
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Попытка удаления пользователем с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Попытка обновления комментария другого пользователя
     */
    public function testNotOwner()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn();
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertForbidden();
    }

    /**
     * Попытка удаления комментария для публикации, которая не относится к указанному сообществу
     */
    public function testWrongCommunityPostCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost();
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка удаления комментария, который не относится к указанному комментарию
     */
    public function testWrongPostCommentCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['user_id' => $user->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertNotFound();
    }

    /**
     * Попытка удаления комментария, который был создан более суток назад
     */
    public function testExpiredComment()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertForbidden();
    }

    /**
     * Успешное редактирование
     */
    public function testSuccess()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
        $response->assertRedirect($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseMissing('post_comments', [
            'id'   => $comment->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Кнопка удаления не отображается для гостей
     */
    public function testDeleteButtonForGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

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
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

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
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

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
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

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
        $post      = $this->createPost(['community_id' => $community->id]);

        $comment = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);
        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertSee('Delete Comment');
        $response->assertSee($this->buildDeleteUrl($community->slug, $post->slug, $comment->id));
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
     * Формирование пути для обновления комментария
     *
     * @param string $communitySlug
     * @param string $postSlug
     * @param int    $commentId
     *
     * @return string
     */
    protected function buildDeleteUrl(string $communitySlug, string $postSlug, int $commentId): string
    {
        return $this->buildPostShowUrl($communitySlug, $postSlug) . '/comments/' . $commentId;
    }
}
