<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

    /**
     * Просмотр страницы без публикаций
     */
    public function testWithoutPosts()
    {
        $response = $this->get('/');

        $response->assertSee('No posts yet.');
    }

    /**
     * Просмотр страницы с публикациями
     */
    public function testWithPosts()
    {
        $firstPost  = $this->createPost();
        $secondPost = $this->createPost();

        $response = $this->get('/');

        $response->assertSee($firstPost->title);
        $response->assertSee($secondPost->title);
    }

    /**
     * Просмотр страницы с публикациями у которых выведены обрезанные описания
     */
    public function testWithPostsTruncatedTexts()
    {
        $firstText  = $this->faker->text(1000);
        $secondText = $this->faker->text(1000);

        $this->createPost(['text' => $firstText]);
        $this->createPost(['text' => $secondText]);

        $communityPostsTextPreviewLimit = 200;

        $response = $this->get('/');
        $response->assertSee(Str::limit($firstText, $communityPostsTextPreviewLimit));
        $response->assertSee(Str::limit($secondText, $communityPostsTextPreviewLimit));
    }

    /**
     * Отображение публикаций в порядке убывания по времени создания публикаций
     */
    public function testWithLatestPosts()
    {
        $firstPost  = $this->createPost();
        $secondPost = $this->createPost(['created_at' => now()->addHour()]);

        $response = $this->get('/');
        $response->assertSeeInOrder([$secondPost->title, $firstPost->title]);
    }

    /**
     * Просмотр страницы сообщества с публикациями и пагинацией
     */
    public function testWithPostsAndPagination()
    {
        $firstPost  = $this->createPost();
        $secondPost = $this->createPost();
        $thirdPost  = $this->createPost();

        $response = $this->get('/');

        $response->assertSee($firstPost->title);
        $response->assertSee($secondPost->title);
        $response->assertDontSee($thirdPost->title);
    }
}
