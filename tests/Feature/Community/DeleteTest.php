<?php

declare(strict_types=1);

namespace Tests\Feature\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Попытка удаления гостем
     */
    public function testDeleteGuest()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->delete($this->buildDeleteRoute($community->slug));
        $response->assertRedirect('/login');
    }

    /**
     * Попытка удаления пользователем с неподтвержденным email
     */
    public function testDeleteNotVerifiedUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn(User::factory()->unverified()->create());

        $response = $this->delete($this->buildDeleteRoute($community->slug));
        $response->assertRedirect('/verify-email');
    }

    /**
     * Попытка удаления сущности другого пользователя
     */
    public function testDeleteAnotherUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn();

        $response = $this->delete($this->buildDeleteRoute($community->slug));
        $response->assertForbidden();
    }

    /**
     * Попытка удаления несуществующей сущности
     */
    public function testDeleteNotExisted()
    {
        $this->signIn();

        $response = $this->delete($this->buildDeleteRoute('test-test-test'));
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

        $response = $this->delete($this->buildDeleteRoute($community->slug));
        $response->assertRedirect('/communities');
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseMissing('communities', [
            'id'         => $community->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Формирование адреса для удаления сущности
     *
     * @param string $slug
     *
     * @return string
     */
    private function buildDeleteRoute(string $slug): string
    {
        return '/communities/' . $slug;
    }
}
