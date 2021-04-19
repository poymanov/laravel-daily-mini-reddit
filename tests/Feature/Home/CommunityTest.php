<?php

declare(strict_types=1);

namespace Tests\Feature\Home;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Просмотр страницы без сообществ
     */
    public function testEmpty()
    {
        $response = $this->get('/');

        $response->assertSee('No communities yet.');
    }

    /**
     * Просмотр страницы с сообществами
     */
    public function testWithCommunities()
    {
        $community = $this->createCommunity();

        $response = $this->get('/');

        $response->assertSee($community->name);
    }

    /**
     * Отображение сообществ в порядке убывания по времени создания
     */
    public function testWithLatestCommunities()
    {
        $firstCommunity  = $this->createCommunity();
        $secondCommunity = $this->createCommunity(['created_at' => now()->addHour()]);

        $response = $this->get('/');
        $response->assertSeeInOrder([$secondCommunity->name, $firstCommunity->name]);
    }

    /**
     * Отображение ограниченного количества сообществ
     */
    public function testCommunitiesWithLimit()
    {
        $firstCommunity  = $this->createCommunity();
        $secondCommunity = $this->createCommunity();
        $thirdCommunity  = $this->createCommunity();

        $response = $this->get('/');

        $response->assertSee($firstCommunity->name);
        $response->assertSee($secondCommunity->name);
        $response->assertDontSee($thirdCommunity->name);

        $response->assertSee('See all');
    }
}
