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
}
