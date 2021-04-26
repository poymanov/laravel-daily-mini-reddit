<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Форма создания недоступна для гостей
     */
    public function testCreateScreenCannotBeRenderedForGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('New Comment');
        $response->assertDontSee('Post Comment');
    }

    /**
     * Форма создания отображается
     */
    public function testCreateScreenCanBeRendered()
    {
        $this->signIn();

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertOk();

        $response->assertSee($community->name);
        $response->assertSee('New Comment');
        $response->assertSee('Post Comment');
    }

    /**
     * Попытка создания с пустыми данными
     */
    public function testEmpty()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug));
        $response->assertSessionHasErrors(['text']);
    }

    /**
     * Попытка создания со слишком коротким текстом
     */
    public function testTooShortTitle()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug), ['name' => '12']);
        $response->assertSessionHasErrors(['text']);
    }

    /**
     * Попытка создания для несуществующего сообщества
     */
    public function testNotExistedCommunity()
    {
        $post = $this->createPost();

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl('test', $post->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка создания для удаленного сообщества
     */
    public function testDeletedCommunity()
    {
        $community = $this->createCommunity([], true);
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка создания для несуществующей публикации
     */
    public function testNotExistedPost()
    {
        $community = $this->createCommunity();

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, 'test'));
        $response->assertNotFound();
    }

    /**
     * Попытка создания для удаленной публикации
     */
    public function testDeletedPost()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id], true);

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug));
        $response->assertNotFound();
    }

    /**
     * Попытка создания для публикации, которая не относится к указанному сообществу
     */
    public function testWrongPost()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost();

        $this->signIn();

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug), ['text' => '12345']);
        $response->assertNotFound();
    }

    /**
     * Попытка создания пользователем с неподтвердженным профилем
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn(User::factory()->unverified()->create());

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug), ['text' => '12345']);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Успешное добавление комментария
     */
    public function testSuccess()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $user = $this->createUser();
        $this->signIn($user);

        $commentText = '12345';

        $response = $this->post($this->buildStoreCommentUrl($community->slug, $post->slug), ['text' => $commentText]);
        $response->assertRedirect($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('post_comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'text'    => $commentText,
        ]);
    }

    /**
     * Формирование пути для просмотра публикации
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildPostShowUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug;
    }

    /**
     * Формирование пути для просмотра публикации
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildStoreCommentUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug . '/comments';
    }
}
