<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Report;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/profile/reports';

    /**
     * Страница недоступна для гостей
     */
    public function testCannotBeRenderedForGuest()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Страница недоступна для пользователей с неподтвержденным email
     */
    public function testCannotBeRenderedForNotVerifiedUser()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Страница недоступна для пользователей без прав администратора
     */
    public function testCannotBeRenderedForNotAdmin()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn();
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertForbidden();
    }

    /**
     * Попытка открытия несуществующей жалобы
     */
    public function testNotFound()
    {
        $this->signIn();
        $response = $this->get(self::URL . '/999');
        $response->assertNotFound();
    }

    /**
     * Страница отображается для пользователей с правами администратора
     */
    public function testSuccess()
    {
        $post   = $this->createPost();
        $report = $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertSee('Details');
        $response->assertSee('Object');
        $response->assertSee('Type');
        $response->assertSee('ID');
        $response->assertSee('Title');
        $response->assertSee('Content');
        $response->assertSee('Report');
        $response->assertSee('Description');
        $response->assertSee('Reporter');
        $response->assertSee('Created At');
        $response->assertSee('Resolve Report');
        $response->assertOk();
    }

    /**
     * Страница отображается для жалобы на комментарий
     */
    public function testSuccessComment()
    {
        $comment = $this->createPostComment();
        $report  = $this->createReport([
            'reportable_type' => PostComment::class,
            'reportable_id'   => $comment->id,
        ]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertSee($comment->id);
        $response->assertSee($comment->text);
    }

    /**
     * Страница отображается для жалобы на публикацию
     */
    public function testSuccessPost()
    {
        $post   = $this->createPost();
        $report = $this->createReport([
            'reportable_type' => Post::class,
            'reportable_id'   => $post->id,
        ]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertSee($post->id);
        $response->assertSee($post->title);
        $response->assertSee($post->text);
    }

    /**
     * Страница отображается для жалобы на сообщество
     */
    public function testSuccessCommunity()
    {
        $community = $this->createCommunity();
        $report    = $this->createReport([
            'reportable_type' => Community::class,
            'reportable_id'   => $community->id,
        ]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL . '/' . $report->id);
        $response->assertSee($community->id);
        $response->assertSee($community->name);
        $response->assertSee($community->description);
    }

    /**
     * На странице отображается информация по жалобе
     */
    public function testSuccessReportInfo()
    {
        $comment      = $this->createPostComment();
        $reportAuthor = $this->createUser();
        $report       = $this->createReport([
            'reportable_type' => PostComment::class,
            'reportable_id'   => $comment->id,
            'user_id'         => $reportAuthor->id,
        ]);

        $this->signIn($this->createAdmin());
        $response = $this->get(self::URL . '/' . $report->id);

        $response->assertSee($report->text);
        $response->assertSee($reportAuthor->name . ' (ID: ' . $reportAuthor->id . ')');
        $response->assertSee($report->created_at);
    }
}
