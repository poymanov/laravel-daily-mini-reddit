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
        $post      = $this->createPost();

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
     * Кнопка редактирования не отображается для гостей
     */
    public function testEditButtonGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit post');
    }

    /**
     * Кнопка редактирования не отображается для авторизованных пользователей, не авторов публикации
     */
    public function testEditButtonAnotherAuthUser()
    {
        $this->signIn();
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Edit post');
    }

    /**
     * Кнопка редактирования отображается для автор публикации
     */
    public function testEditButtonAuthor()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertSee('Edit post');
        $response->assertSee('/communities/' . $community->slug . '/posts/' . $post->slug . '/edit');
    }

    /**
     * Кнопка удаления не отображается для гостей
     */
    public function testDeleteButtonGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Delete post');
    }

    /**
     * Кнопка удаления не отображается для авторизованных пользователей, не авторов публикации
     */
    public function testDeleteButtonAnotherAuthUser()
    {
        $this->signIn();
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertDontSee('Delete post');
    }

    /**
     * Кнопка редактирования отображается для автор публикации
     */
    public function testDeleteButtonAuthor()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user]);

        $response = $this->get($this->buildShowUrl($community->slug, $post->slug));

        $response->assertSee('Delete post');
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
