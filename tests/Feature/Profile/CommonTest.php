<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommonTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/profile';

    /**
     * Страница недоступна для гостей
     */
    public function testCannotBeRenderedForGuest()
    {
        $response = $this->get(self::URL);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Страница недоступна для пользователей с неподтвержденным email
     */
    public function testCannotBeRenderedForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get(self::URL);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Пункт меню управления жалобами недоступен для пользователей без прав администратора
     */
    public function testReportMenuItemCannotBeRenderedForNotAdmin()
    {
        $this->signIn();
        $response = $this->get(self::URL);
        $response->assertDontSee('Reports');
        $response->assertDontSee('profile/reports');
    }

    /**
     * Страница отображается для авторизованных пользователей
     */
    public function testSuccess()
    {
        $this->signIn();
        $response = $this->get(self::URL);
        $response->assertOk();
    }
}
