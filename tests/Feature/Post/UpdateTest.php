<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Форма редактирования недоступна для гостей
     */
    public function testUpdateScreenCannotBeRenderedForGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildEditUrl($community->slug, $post->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Форма редактирования недоступна для пользователей с неподтвержденным email
     */
    public function testUpdateScreenCannotBeRenderedForNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $user      = $this->createUser([], true);
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user]);

        $this->signIn($user);
        $response = $this->get($this->buildEditUrl($community->slug, $post->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Форма редактирования сущности, созданной другим пользователем, недоступна
     */
    public function testUpdateScreenCannotBeRenderedForAnotherUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();
        $response = $this->get($this->buildEditUrl($community->slug, $post->slug));

        $response->assertForbidden();
    }

    /**
     * Форма редактирования сущности недоступна, если публикация не отновится к сообществу
     */
    public function testUpdateScreenCannotBeRenderedForAnotherCommunity()
    {
        $user      = $this->createUser();
        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id]);

        $this->signIn($user);
        $response = $this->get($this->buildEditUrl($community->slug, $post->slug));

        $response->assertNotFound();
    }

    /**
     * Форма редактирования отображается
     */
    public function testUpdateScreenCanBeRendered()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->get($this->buildEditUrl($community->slug, $post->slug));
        $response->assertOk();

        $response->assertSee('Title');
        $response->assertSee('Text');
        $response->assertSee('Url');

        $response->assertSee($post->title);
        $response->assertSee($post->text);
        $response->assertSee($post->url);

        $response->assertSee('Update');
    }

    /**
     * Попытка изменения с пустыми данными
     */
    public function testEmpty()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug));
        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Попытка изменения со слишком коротким наименованием
     */
    public function testTooShortTitle()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), ['title' => '12']);
        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Попытка изменения со слишком коротким наименованием
     */
    public function testWrongUrl()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), ['url' => '12']);
        $response->assertSessionHasErrors(['url']);
    }

    /**
     * Попытка изменения публикации из удаленного сообщества
     */
    public function testDeletedCommunity()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity([], true);
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());
        $response->assertNotFound();
    }

    /**
     * Попытка изменения удаленной публикации
     */
    public function testDeletedPost()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id], true);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());
        $response->assertNotFound();
    }

    /**
     * Попытка изменения публикации с указанными несуществующим сообществом
     */
    public function testNotExistedCommunity()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $post = $this->createPost(['user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl('test', $post->slug), $post->toArray());
        $response->assertNotFound();
    }

    /**
     * Попытка изменения несуществующей публикации
     */
    public function testNotExistedPost()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();

        $response = $this->patch($this->buildUpdateUrl($community->slug, 'test'));
        $response->assertNotFound();
    }

    /**
     * Попытка изменения публикации, не относящейся к указанному сообществу
     */
    public function testAnotherCommunity()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());
        $response->assertNotFound();
    }

    /**
     * Попытка изменения публикации другого пользователя
     */
    public function testAnotherUser()
    {
        $user = $this->createUser();

        $this->signIn();

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());
        $response->assertForbidden();
    }

    /**
     * Успешное изменение публикации
     */
    public function testSuccess()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $newTitle = 'New title';
        $newText  = 'New text';
        $newUrl   = 'https://test.test';

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), [
            'title' => $newTitle,
            'text'  => $newText,
            'url'   => $newUrl,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseHas('posts', [
            'id'    => $post->id,
            'title' => $newTitle,
            'text'  => $newText,
            'url'   => $newUrl,
            'slug'  => 'new-title',
        ]);
    }

    /**
     * Успешное изменение публикации со старыми значениями
     */
    public function testSuccessWithSameData()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());
    }

    /**
     * Успешное изменение публикации без указания текста
     */
    public function testSuccessWithoutText()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id, 'text' => null]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseHas('posts', [
            'id'   => $post->id,
            'text' => null,
        ]);
    }

    /**
     * Успешное изменение публикации без указания url
     */
    public function testSuccessWithoutUrl()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['user_id' => $user->id, 'community_id' => $community->id, 'url' => null]);

        $response = $this->patch($this->buildUpdateUrl($community->slug, $post->slug), $post->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseHas('posts', [
            'id'  => $post->id,
            'url' => null,
        ]);
    }

    /**
     * Формирование пути для редактирования сообщества
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildEditUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug . '/edit';
    }

    /**
     * Формирование пути для изменения сообщества
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildUpdateUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug;
    }

    /**
     * Формирование общего пути для сообщества
     *
     * @return string
     */
    protected function buildCommunitiesUrl(): string
    {
        return '/communities';
    }
}
