<?php

declare(strict_types=1);

namespace Tests\Feature\Home;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

    /**
     * Просмотр с публикациями у которых есть изображения
     */
    public function testWithPostsAndImages()
    {
        $firstPost  = $this->createPost();
        $secondPost = $this->createPost();

        $firstLargeImage  = $this->createPostImage(['type' => 'large', 'post_id' => $firstPost->id]);
        $secondLargeImage = $this->createPostImage(['type' => 'large', 'post_id' => $secondPost->id]);

        $response = $this->get('/');

        $response->assertSee($firstLargeImage->name);
        $response->assertSee($secondLargeImage->name);
    }

    /**
     * Гость не видит кнопки голосования за пост
     */
    public function testGuestDontSeeVoteButtons()
    {
        $this->createPost();

        $response = $this->get('/');

        $response->assertDontSee('Like post');
        $response->assertDontSee('Dislike post');
    }

    /**
     * Автор поста не видит кнопки голосования за пост
     */
    public function testPostAuthorDontSeeVoteButtons()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $this->createPost(['user_id' => $user->id]);

        $response = $this->get('/');

        $response->assertDontSee('Like post');
        $response->assertDontSee('Dislike post');
    }

    /**
     * Авторизованный пользователь видит кнопки голосования за пост
     */
    public function testAuthSeeVoteButtons()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $this->createPost();

        $response = $this->get('/');

        $response->assertSee('Like post');
        $response->assertSee('Dislike post');
    }

    /**
     * Если пользователь поставил публикации лайк, он не видит этой кнопки
     */
    public function testDontSeeLikeButton()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $post = $this->createPost();
        $this->createPostVote(['post_id' => $post->id, 'user_id' => $user->id, 'vote' => 1]);

        $response = $this->get('/');

        $response->assertDontSee('Like post');
        $response->assertSee('Dislike post');
    }

    /**
     * Если пользователь поставил публикации дизлайк, он не видит этой кнопки
     */
    public function testDontSeeDislikeButton()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $post = $this->createPost();
        $this->createPostVote(['post_id' => $post->id, 'user_id' => $user->id, 'vote' => -1]);

        $response = $this->get('/');

        $response->assertSee('Like post');
        $response->assertDontSee('Dislike post');
    }
}
