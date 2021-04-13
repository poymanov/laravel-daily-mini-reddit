<?php

declare(strict_types=1);

namespace Tests\Feature\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Страница всех сущностей недоступна для гостей
     */
    public function testIndexScreenCannotBeRenderedForGuest()
    {
        $response = $this->get('/communities');
        $response->assertRedirect('/login');
    }

    /**
     * Страница всех сущностей недоступна для пользователей с неподтвержденным email
     */
    public function testIndexScreenCannotBeRenderedForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get('/communities');
        $response->assertRedirect('/verify-email');
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

        $response = $this->get('/communities');
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

        $response = $this->get('/communities');


        $response->assertSee($firstCommunity->name);
        $response->assertSee($secondCommunity->name);
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

        $response = $this->get('/communities');

        $response->assertDontSee($firstCommunity->name);
        $response->assertDontSee($secondCommunity->name);
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

        $response = $this->get('/communities');

        $response->assertDontSee($firstCommunity->name);
        $response->assertDontSee($secondCommunity->name);
    }
}
