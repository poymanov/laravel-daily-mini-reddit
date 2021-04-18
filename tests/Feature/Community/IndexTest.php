<?php

declare(strict_types=1);

namespace Tests\Feature\Community;

use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Успешное отображение страницы с сообществами
     */
    public function testScreenCanBeRendered()
    {
        $response = $this->get('/communities');
        $response->assertOk();

        $response->assertSee('Communities');
    }

    /**
     * Успешное отображение сущностей в порядке - последние первыми
     */
    public function testSuccessLatest()
    {
        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->create();

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->create(['created_at' => now()->addHour()]);

        $response = $this->get('/communities');

        $response->assertSeeInOrder([$secondCommunity->name, $firstCommunity->name]);
    }

    /**
     * Успешное отображение сущностей с пагинацией в порядке - последние первыми
     */
    public function testSuccessLatestWithPagination()
    {
        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->create();

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->create(['created_at' => now()->addHour()]);

        /** @var Community $thirdCommunity */
        $thirdCommunity = Community::factory()->create(['created_at' => now()->addHours(2)]);

        $response = $this->get('/communities');

        $response->assertSeeInOrder([$thirdCommunity->name, $secondCommunity->name]);
        $response->assertDontSee($firstCommunity->name);
    }

    /**
     * Удаленные сущности не отображаются
     */
    public function testDeleted()
    {
        /** @var Community $deletedCommunity */
        $deletedCommunity = Community::factory()->deleted()->create();

        $response = $this->get('/communities');
        $response->assertDontSee($deletedCommunity->name);
    }
}
