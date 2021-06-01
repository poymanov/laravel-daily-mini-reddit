<?php

declare(strict_types=1);

namespace Tests\Feature\Report;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ссылка на жалобу недоступна гостям
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report Post');
    }

    /**
     * Ссылка на жалобу недоступна пользователям с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn($this->createUser([], true));

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report Post');
    }

    /**
     * Ссылка на жалобу недоступна автору комментария
     */
    public function testOwner()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report Post');
    }

    /**
     * Ссылка на жалобу успешно отображается
     */
    public function testSuccess()
    {
        $this->signIn($this->createUser());

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSee('Report Post');
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
}
