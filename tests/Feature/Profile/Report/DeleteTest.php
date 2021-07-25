<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Report;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/profile/reports';

    /**
     * Удаление недоступно для гостей
     */
    public function testGuest()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $response = $this->delete(self::URL . '/' . $report->id);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Удаление недоступно для пользователей с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->delete(self::URL . '/' . $report->id);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Удаление недоступно для пользователей без прав администратора
     */
    public function testNotAdmin()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn();
        $response = $this->delete(self::URL . '/' . $report->id);
        $response->assertForbidden();
    }

    /**
     * Попытка удаления несуществующей жалобы
     */
    public function testNotFound()
    {
        $this->signIn($this->createAdmin());
        $response = $this->delete(self::URL . '/999');
        $response->assertNotFound();
    }

    /**
     * Попытка удаления уже существующей жалобы
     */
    public function testAlreadyDeleted()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id], true);

        $this->signIn($this->createAdmin());
        $response = $this->delete(self::URL . '/' . $report->id);
        $response->assertNotFound();
    }

    /**
     * Успешное удаление жалобы
     */
    public function testSuccess()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn($this->createAdmin());
        $this->delete(self::URL . '/' . $report->id);

        $this->assertDatabaseMissing('reports', [
            'id'         => $report->id,
            'deleted_at' => null,
        ]);
    }
}
