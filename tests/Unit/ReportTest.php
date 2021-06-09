<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Report;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Получение заголовка для жалобы на комментарий
     */
    public function testCommentLabel()
    {
        /** @var Report $commentReport */
        $commentReport = Report::factory()->make(['reportable_type' => PostComment::class]);
        $this->assertEquals('Comment', $commentReport->getTypeLabel());
    }

    /**
     * Получение заголовка для жалобы на публикацию
     */
    public function testPostLabel()
    {
        /** @var Report $commentReport */
        $commentReport = Report::factory()->make(['reportable_type' => Post::class]);
        $this->assertEquals('Post', $commentReport->getTypeLabel());
    }

    /**
     * Получение заголовка для жалобы на сообщество
     */
    public function testCommunityLabel()
    {
        /** @var Report $commentReport */
        $commentReport = Report::factory()->make(['reportable_type' => Community::class]);
        $this->assertEquals('Community', $commentReport->getTypeLabel());
    }

    /**
     * Попытка получения заголовка неизвестного типа
     */
    public function testUnknownTypeLabel()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wrong report type');

        /** @var Report $commentReport */
        $commentReport = Report::factory()->make(['reportable_type' => 'test']);
        $commentReport->getTypeLabel();
    }
}
