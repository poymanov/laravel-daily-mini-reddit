<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Попытка обновления гостем
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Попытка обновления пользователем с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
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
        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
        $response->assertForbidden();
    }

    /**
     * Попытка обновления комментария, который не относится к указанному комментарию
     */
    public function testWrongPostCommentCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
        $response->assertNotFound();
    }

    /**
     * Попытка обновления комментария, который был создан более суток назад
     */
    public function testExpiredComment()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id, 'created_at' => now()->subDays(2)]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
        $response->assertForbidden();
    }

    /**
     * Попытка обновления комментария для публикации, которая не относится к указанному сообществу
     */
    public function testWrongCommunityPostCombination()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost();
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '12345']);
        $response->assertNotFound();
    }

    /**
     * Попытка без указания текста
     */
    public function testEmpty()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), []);
        $response->assertSessionHasErrors(['text']);
    }

    /**
     * Попытка с указанием слишком короткого текста
     */
    public function testToShortText()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => '123']);
        $response->assertSessionHasErrors(['text']);
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

        $newText = '12345';

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug, $comment->id), ['text' => $newText]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('post_comments', [
            'id'   => $comment->id,
            'text' => $newText,
        ]);
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
    protected function buildUpdateUrl(string $communitySlug, string $postSlug, int $commentId): string
    {
        return $this->buildPostShowUrl($communitySlug, $postSlug) . '/comments/' . $commentId;
    }
}
