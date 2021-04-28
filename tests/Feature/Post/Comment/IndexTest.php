<?php

declare(strict_types=1);

namespace Tests\Feature\Post\Comment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Если у публикации нет комментариев, блок с комментариями не отображается
     */
    public function testWithoutComments()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertDontSee('Comments');
    }

    /**
     * Отображение заголовка комментариев
     */
    public function testCommentsTitle()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSee('Comments: 1');
    }

    /**
     * Отображение данных комментария
     */
    public function testCommentData()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $user      = $this->createUser();
        $comment   = $this->createPostComment(['post_id' => $post->id, 'user_id' => $user->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));
        $response->assertSee($comment->text);
        $response->assertSee('Author: ' . $user->name);
        $response->assertSee('Created: ' . $comment->created_at->diffForHumans());
    }

    /**
     * Отображение с учётом пагинации
     */
    public function testPagination()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $commentFirst  = $this->createPostComment(['post_id' => $post->id]);
        $commentSecond = $this->createPostComment(['post_id' => $post->id]);
        $commentThird  = $this->createPostComment(['post_id' => $post->id]);

        $response = $this->get($this->buildPostShowUrl($community->slug, $post->slug));

        $response->assertSee($commentFirst->text);
        $response->assertSee($commentSecond->text);
        $response->assertDontSee($commentThird->text);
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
