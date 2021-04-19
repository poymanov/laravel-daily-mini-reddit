<?php

declare(strict_types=1);

namespace Tests\Feature\Home;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Успешное отображение главной страницы
     */
    public function testScreenCanBeRendered()
    {
        $response = $this->get('/');
        $response->assertOk();

        $response->assertSee('Mini Reddit');
    }

    /**
     * Отображение страницы для гостей
     */
    public function testCanBeRenderedGuest()
    {
        $response = $this->get('/');
        $response->assertSee('Log in');
        $response->assertSee('Register');
        $response->assertDontSee('Profile');
        $response->assertDontSee('Log out');
    }

    /**
     * Отображение страницы для авторизованных пользователей
     */
    public function testCanBeRenderedAuth()
    {
        $this->signIn();

        $response = $this->get('/');
        $response->assertSee('Profile');
        $response->assertSee('Log out');
        $response->assertDontSee('Log in');
        $response->assertDontSee('Register');
    }
}
