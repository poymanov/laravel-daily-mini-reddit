<?php

declare(strict_types=1);

namespace Tests\Feature\Community;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Успешное отображение главной страницы
     */
    public function testScreenCanBeRendered()
    {
        $community = $this->createCommunity();

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertOk();

        $response->assertSee($community->name);
        $response->assertSee($community->description);
    }

    /**
     * Попытка просмотра страницы несуществующего сообщества
     */
    public function testNotExisted()
    {
        $response = $this->get($this->buildShowUrl('test'));
        $response->assertNotFound();
    }

    /**
     * Просмотр страницы сообщества без публикаций
     */
    public function testWithoutPosts()
    {
        $community = $this->createCommunity();

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee('No posts yet.');
    }

    /**
     * Просмотр страницы сообщества с публикациями
     */
    public function testWithPosts()
    {
        $community  = $this->createCommunity();
        $firstPost  = $this->createPost(['community_id' => $community->id]);
        $secondPost = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee($firstPost->title);
        $response->assertSee($secondPost->title);
    }

    /**
     * Просмотр страницы сообщества с публикациями у которых выведены обрезанные описания
     */
    public function testWithPostsTruncatedTexts()
    {
        $community = $this->createCommunity();

        $firstText  = $this->faker->text(1000);
        $secondText = $this->faker->text(1000);

        $this->createPost(['community_id' => $community->id, 'text' => $firstText]);
        $this->createPost(['community_id' => $community->id, 'text' => $secondText]);

        $communityPostsTextPreviewLimit = 200;

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee(Str::limit($firstText, $communityPostsTextPreviewLimit));
        $response->assertSee(Str::limit($secondText, $communityPostsTextPreviewLimit));
    }

    /**
     * Отображение публикаций в порядке убывания по времени создания публикаций
     */
    public function testWithLatestPosts()
    {
        $community  = $this->createCommunity();
        $firstPost  = $this->createPost(['community_id' => $community->id]);
        $secondPost = $this->createPost(['community_id' => $community->id, 'created_at' => now()->addHour()]);

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSeeInOrder([$secondPost->title, $firstPost->title]);
    }

    /**
     * Просмотр страницы сообщества с публикациями и пагинацией
     */
    public function testWithPostsAndPagination()
    {
        $community  = $this->createCommunity();
        $firstPost  = $this->createPost(['community_id' => $community->id]);
        $secondPost = $this->createPost(['community_id' => $community->id]);
        $thirdPost  = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee($firstPost->title);
        $response->assertSee($secondPost->title);
        $response->assertDontSee($thirdPost->title);
    }

    /**
     * Просмотр страницы сообщества с публикациями, у которых есть изображения
     */
    public function testWithPostsAndImages()
    {
        $community  = $this->createCommunity();
        $firstPost  = $this->createPost(['community_id' => $community->id]);
        $secondPost = $this->createPost(['community_id' => $community->id]);

        $firstLargeImage  = $this->createPostImage(['type' => 'large', 'post_id' => $firstPost->id]);
        $secondLargeImage = $this->createPostImage(['type' => 'large', 'post_id' => $secondPost->id]);

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee($firstLargeImage->name);
        $response->assertSee($secondLargeImage->name);
    }

    /**
     * Формирование пути для просмотра сообщества
     *
     * @param string $communitySlug
     *
     * @return string
     */
    protected function buildShowUrl(string $communitySlug): string
    {
        return '/communities/' . $communitySlug;
    }
}
