<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends CommunityTestCase
{
    use RefreshDatabase;

    /**
     * Попытка удаления гостем
     */
    public function testDeleteGuest()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->delete($this->buildDeleteUrl($community->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Попытка удаления пользователем с неподтвержденным email
     */
    public function testDeleteNotVerifiedUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn(User::factory()->unverified()->create());

        $response = $this->delete($this->buildDeleteUrl($community->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Попытка удаления сущности другого пользователя
     */
    public function testDeleteAnotherUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn();

        $response = $this->delete($this->buildDeleteUrl($community->slug));
        $response->assertForbidden();
    }

    /**
     * Попытка удаления несуществующей сущности
     */
    public function testDeleteNotExisted()
    {
        $this->signIn();

        $response = $this->delete($this->buildDeleteUrl('test-test-test'));
        $response->assertNotFound();
    }

    /**
     * Успешное удаление
     */
    public function testSuccess()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $community */
        $community = Community::factory()->create(['user_id' => $user->id]);

        $response = $this->delete($this->buildDeleteUrl($community->slug));
        $response->assertRedirect('/profile/communities');
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseMissing('communities', [
            'id'         => $community->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Успешное удаление сообщества администратором
     */
    public function testSuccessAdmin()
    {
        $this->signIn($this->createAdmin());

        $community = $this->createCommunity();

        $response = $this->delete($this->buildDeleteUrl($community->slug));
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseMissing('communities', [
            'id'         => $community->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Успешное удаление с публикациями и комментариями к ним
     */
    public function testSuccessWithPostsAndComments()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        $community = $this->createCommunity(['user_id' => $user->id]);
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $this->delete($this->buildDeleteUrl($community->slug));

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('posts', ['id' => $post->id, 'deleted_at' => null]);

        $this->assertDatabaseHas('post_comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('post_comments', ['id' => $comment->id, 'deleted_at' => null]);
    }
}
