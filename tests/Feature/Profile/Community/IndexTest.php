<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Community;

use App\Models\Community;
use App\Models\User;
use App\Services\CommunityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;

class IndexTest extends CommunityTestCase
{
    use RefreshDatabase;

    /**
     * Страница всех сущностей недоступна для гостей
     */
    public function testIndexScreenCannotBeRenderedForGuest()
    {
        $response = $this->get(self::COMMON_URL);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Страница всех сущностей недоступна для пользователей с неподтвержденным email
     */
    public function testIndexScreenCannotBeRenderedForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get(self::COMMON_URL);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Отображение страницы всех сущностей
     */
    public function testIndexScreenCannotBeRendered()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        Community::factory()->create(['user_id' => $user->id]);

        $response = $this->get(self::COMMON_URL);
        $response->assertOk();

        $response->assertSee('Create');
        $response->assertSee('Name');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
    }

    /**
     * Успешное отображение всех сущностей
     */
    public function testSuccess()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->create(['user_id' => $user->id]);

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->create(['user_id' => $user->id]);

        $response = $this->get(self::COMMON_URL);


        $response->assertSee($firstCommunity->name);
        $response->assertSee($secondCommunity->name);
    }

    /**
     * Успешное отображение всех сущностей с учетом пагинации
     */
    public function testSuccessWithPagination()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->create(['user_id' => $user->id]);

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->create(['user_id' => $user->id]);

        /** @var Community $thirdCommunity */
        $thirdCommunity = Community::factory()->create(['user_id' => $user->id]);

        $response = $this->get(self::COMMON_URL);

        $response->assertSee($firstCommunity->name);
        $response->assertSee($secondCommunity->name);
        $response->assertDontSee($thirdCommunity->name);
    }

    /**
     * Сущности другого пользователя не отображаются
     */
    public function testCannotBeRenderedAnotherUser()
    {
        $this->signIn();

        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->create();

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->create();

        $response = $this->get(self::COMMON_URL);

        $response->assertDontSee($firstCommunity->name);
        $response->assertDontSee($secondCommunity->name);
    }

    /**
     * Отображение любых сообществ для администратора
     */
    public function testSuccessAdmin()
    {
        $this->signIn($this->createAdmin());

        $community = $this->createCommunity();

        $response = $this->get(self::COMMON_URL);

        $response->assertSee($community->name);
    }

    /**
     * Удаленные сущности не отображаются
     */
    public function testCannotBeRenderedDeleted()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $firstCommunity */
        $firstCommunity = Community::factory()->deleted()->create(['user_id' => $user->id]);

        /** @var Community $secondCommunity */
        $secondCommunity = Community::factory()->deleted()->create(['user_id' => $user->id]);

        $response = $this->get(self::COMMON_URL);

        $response->assertDontSee($firstCommunity->name);
        $response->assertDontSee($secondCommunity->name);
    }
}
