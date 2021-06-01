<?php

declare(strict_types=1);

namespace Tests\Feature\Report;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ссылка на жалобу на комментарий недоступна гостям
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report comment');
    }

    /**
     * Ссылка на жалобу на комментарий недоступна пользователям с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostComment(['post_id' => $post->id]);

        $this->signIn($this->createUser([], true));

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report comment');
    }

    /**
     * Ссылка на жалобу на комментарий недоступна автору комментария
     */
    public function testOwner()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Report comment');
    }

    /**
     * Ссылка на жалобу успешно отображается
     */
    public function testSuccess()
    {
        $this->signIn($this->createUser());

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSee('Report Comment');
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
