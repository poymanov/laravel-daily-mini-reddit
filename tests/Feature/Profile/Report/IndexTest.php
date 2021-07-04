<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Report;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const URL = '/profile/reports';

    /**
     * Страница недоступна для гостей
     */
    public function testCannotBeRenderedForGuest()
    {
        $response = $this->get(self::URL);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Страница недоступна для пользователей с неподтвержденным email
     */
    public function testCannotBeRenderedForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get(self::URL);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Страница недоступна для пользователей без прав администратора
     */
    public function testCannotBeRenderedForNotAdmin()
    {
        $this->signIn();
        $response = $this->get(self::URL);
        $response->assertForbidden();
    }

    /**
     * Страница отображается для пользователей с правами администратора
     */
    public function testSuccess()
    {
        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);
        $response->assertOk();
        $response->assertSee('Reports');
        $response->assertSee('Object');
        $response->assertSee('Description');
        $response->assertSee('Created At');
    }

    /**
     * Отображение жалобы на комментарий
     */
    public function testComment()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $comment   = $this->createPostComment(['post_id' => $post->id]);

        $report = $this->createReport(['reportable_type' => PostComment::class, 'reportable_id' => $comment->id]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);
        $response->assertSee('View');
        $response->assertSee('/profile/reports/' . $report->id);
        $response->assertSee('Comment #' . $comment->id);
        $response->assertSee($report->text);
        $response->assertSee($report->created_at);
    }

    /**
     * Отображение жалобы на публикацию
     */
    public function testPost()
    {
        $post = $this->createPost();

        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);
        $response->assertSee('View');
        $response->assertSee('/profile/reports/' . $report->id);
        $response->assertSee('Post #' . $post->id);
        $response->assertSee($report->text);
        $response->assertSee($report->created_at);
    }

    /**
     * Отображение жалобы на сообщество
     */
    public function testCommunity()
    {
        $community = $this->createCommunity();

        $report = $this->createReport(['reportable_type' => Community::class, 'reportable_id' => $community->id]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);
        $response->assertSee('View');
        $response->assertSee('/profile/reports/' . $report->id);
        $response->assertSee('Community #' . $community->id);
        $response->assertSee($report->text);
        $response->assertSee($report->created_at);
    }

    /**
     * Отображение жалоб с учетом пагинации
     */
    public function testPagination()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost();
        $comment   = $this->createPostComment();

        $this->createReport(['reportable_type' => Community::class, 'reportable_id' => $community->id]);
        $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);
        $this->createReport(['reportable_type' => PostComment::class, 'reportable_id' => $comment->id]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);

        $response->assertSee('Community #' . $community->id);
        $response->assertSee('Post #' . $post->id);
        $response->assertDontSee('Comment #' . $comment->id);
    }

    /**
     * Отображение жалоб в порядке убывания по времени создания
     */
    public function testLatest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost();

        $this->createReport(['reportable_type' => Community::class, 'reportable_id' => $community->id]);
        $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id, 'created_at' => now()->addHour()]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL);

        $response->assertSeeInOrder(['Post #' . $post->id, 'Community #' . $community->id]);
    }
}
