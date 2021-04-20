<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Форма создания недоступна для гостей
     */
    public function testCreateScreenCannotBeRenderedForGuest()
    {
        $community = $this->createCommunity();

        $response = $this->get($this->buildCreateUrl($community->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Форма создания недоступна для пользователей с неподтвержденным email
     */
    public function testCreateScreenCannotBeRenderedForNotVerifiedUser()
    {
        $community = $this->createCommunity();

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get($this->buildCreateUrl($community->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Форма создания отображается
     */
    public function testCreateScreenCanBeRendered()
    {
        $this->signIn();

        $community = $this->createCommunity();

        $response = $this->get($this->buildCreateUrl($community->slug));
        $response->assertOk();

        $response->assertSee($community->name);
        $response->assertSee('Title');
        $response->assertSee('Text');
        $response->assertSee('Url');
        $response->assertSee('Image');
        $response->assertSee('Create');
    }

    /**
     * Форма создания недоступна для удаленных Community
     */
    public function testCreateScreenCannotBeRenderedForDeletedCommunity()
    {
        $community = $this->createCommunity([], true);

        $this->signIn();
        $response = $this->get($this->buildCreateUrl($community->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка создания с пустыми данными
     */
    public function testEmpty()
    {
        $community = $this->createCommunity();

        $this->signIn();

        $response = $this->post($this->buildCommonUrl($community->slug));
        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Попытка создания со слишком коротким наименованием
     */
    public function testTooShortTitle()
    {
        $community = $this->createCommunity();

        $this->signIn();

        $response = $this->post($this->buildCommonUrl($community->slug), ['name' => '12']);
        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Попытка создания с неправильным url
     */
    public function testWrongUrl()
    {
        $community = $this->createCommunity();

        $this->signIn();

        $response = $this->post($this->buildCommonUrl($community->slug), ['url' => '12']);
        $response->assertSessionHasErrors(['url']);
    }

    /**
     * Форма создания для удаленных сообществ
     */
    public function testDeletedCommunity()
    {
        $community = $this->createCommunity([], true);
        $post      = $this->makePost();

        $this->signIn();
        $response = $this->post($this->buildCommonUrl($community->slug), $post->toArray());
        $response->assertNotFound();
    }

    /**
     * Успешное создание
     */
    public function testSuccess()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->makePost(['title' => 'Test test']);

        $response = $this->post($this->buildCommonUrl($community->slug), $post->toArray());
        $response->assertRedirect($this->buildCommunitiesUrl());
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('posts', [
            'community_id' => $community->id,
            'user_id'      => $user->id,
            'title'        => $post->title,
            'text'         => $post->text,
            'url'          => $post->url,
            'slug'         => 'test-test',
        ]);
    }

    /**
     * Успешное создание без указания текста публикации
     */
    public function testSuccessWithoutText()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->makePost(['text' => null]);

        $response = $this->post($this->buildCommonUrl($community->slug), $post->toArray());
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseHas('posts', [
            'community_id' => $community->id,
            'user_id'      => $user->id,
            'title'        => $post->title,
            'text'         => null,
            'url'          => $post->url,
        ]);
    }

    /**
     * Успешное создание без указания url публикации
     */
    public function testSuccessWithoutUrl()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->makePost(['url' => null]);

        $response = $this->post($this->buildCommonUrl($community->slug), $post->toArray());
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertDatabaseHas('posts', [
            'community_id' => $community->id,
            'user_id'      => $user->id,
            'title'        => $post->title,
            'text'         => $post->text,
            'url'          => null,
        ]);
    }

    /**
     * Успешное создание с загрузкой изображения
     */
    public function testSuccessWithImage()
    {
        Storage::fake('public');

        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->makePost();

        $response = $this->post($this->buildCommonUrl($community->slug), $post->toArray() + ['image' => UploadedFile::fake()->image('photo1.jpg')]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect($this->buildCommunitiesUrl());

        $this->assertCount(2, Storage::disk('public')->allFiles());

        $this->assertDatabaseCount('post_images', 2);
    }

    /**
     * Формирование пути для создания сущности
     *
     * @param string $communitySlug
     *
     * @return string
     */
    protected function buildCreateUrl(string $communitySlug): string
    {
        return '/communities/' . $communitySlug . '/posts/create';
    }

    /**
     * Формирование общего пути для публикаций сообщества
     *
     * @param string $communitySlug
     *
     * @return string
     */
    protected function buildCommonUrl(string $communitySlug): string
    {
        return '/communities/' . $communitySlug . '/posts';
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
