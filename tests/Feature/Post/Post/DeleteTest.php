<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Попытка удаления гостем
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Попытка удаления пользователем с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $user      = $this->createUser([], true);
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user]);

        $this->signIn($user);
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Попытка удаления публикации другого пользователя
     */
    public function testAnotherUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug));

        $response->assertForbidden();
    }

    /**
     * Попытка удаления публикации из другого сообщества
     */
    public function testAnotherCommunity()
    {
        $user      = $this->createUser();
        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id]);

        $this->signIn($user);
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug));

        $response->assertNotFound();
    }

    /**
     * Успешное удаление
     */
    public function testSuccess()
    {
        $user      = $this->createUser();
        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $this->signIn($user);
        $response = $this->delete($this->buildDeleteUrl($community->slug, $post->slug));

        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseMissing('posts', [
            'id'         => $post->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Удаление публикации вместе с комментариями
     */
    public function testSuccessWithComments()
    {
        $user      = $this->createUser();
        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);
        $firstComment = $this->createPostComment(['post_id' => $post->id]);
        $secondComment = $this->createPostComment(['post_id' => $post->id]);

        $this->signIn($user);
        $this->delete($this->buildDeleteUrl($community->slug, $post->slug));

        $this->assertDatabaseHas('post_comments', [
            'id' => $firstComment->id
        ]);

        $this->assertDatabaseHas('post_comments', [
            'id' => $secondComment->id
        ]);

        $this->assertDatabaseMissing('post_comments', [
            'id' => $firstComment->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseMissing('post_comments', [
            'id' => $secondComment->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Формирование пути для удаления публикации
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildDeleteUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug;
    }

    /**
     * Формирование общего пути для сообщества
     *
     * @return string
     */
    protected function buildCommunitiesUrl(): string
    {
        return '/communities';
    }
}
