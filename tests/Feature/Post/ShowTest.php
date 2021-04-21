<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Попытка просмотра несуществующей публикации
     */
    public function testNotExisted()
    {
        $community = $this->createCommunity();

        $response = $this->get($this->buildShowUrl($community->slug, 'test'));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра публикации из несуществующего сообщества
     */
    public function testNotExistedCommunity()
    {
        $post = $this->createPost();

        $response = $this->get($this->buildShowUrl('test', $post->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра публикации из другого сообщества
     */
    public function testAnotherCommunity()
    {
        $community = $this->createCommunity();
        $post = $this->createPost();

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка просмотра удаленной публикации
     */
    public function testDelete()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id], true);

        $response = $this->get($this->buildShowUrl($community->slug, $post->title));
        $response->assertNotFound();
    }

    /**
     * Просмотр публикации
     */
    public function testSuccess()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));
        $response->assertOk();

        $response->assertSee($community->name);
        $response->assertSee($community->description);

        $response->assertSee($post->title);
        $response->assertSee($post->text);
        $response->assertSee($post->url);
    }

    /**
     * Просмотр публикации с изображением
     */
    public function testSuccessWithImage()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $postLargeImage = $this->createPostImage(['type' => 'large', 'post_id' => $post->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));
        $response->assertOk();

        $response->assertSee($postLargeImage->name);
    }

    /**
     * Формирование пути для просмотра публикации
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildShowUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug;
    }
}
