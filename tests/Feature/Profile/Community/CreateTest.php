<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CreateTest extends CommunityTestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Форма создания недоступна для гостей
     */
    public function testCreateScreenCannotBeRenderedForGuest()
    {
        $response = $this->get($this->buildCreateUrl());
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Форма создания недоступна для пользователей с неподтвержденным email
     */
    public function testCreateScreenCannotBeRenderedForNotVerifiedUser()
    {
        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get($this->buildCreateUrl());
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Форма создания отображается
     */
    public function testCreateScreenCanBeRendered()
    {
        $this->signIn();

        $response = $this->get($this->buildCreateUrl());
        $response->assertOk();

        $response->assertSee('Name');
        $response->assertSee('Description');
        $response->assertSee('Create');
    }

    /**
     * Попытка создания с пустыми данными
     */
    public function testEmpty()
    {
        $this->signIn();

        $response = $this->post(self::COMMON_URL);
        $response->assertSessionHasErrors(['name', 'description']);
    }

    /**
     * Попытка создания со слишком коротким наименованием
     */
    public function testTooShortName()
    {
        $this->signIn();

        $response = $this->post(self::COMMON_URL, ['name' => '12']);
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Попытка создания с уже существующим наименованием
     */
    public function testNotUniqueName()
    {
        $this->signIn();

        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->post(self::COMMON_URL, ['name' => $community->name]);
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Попытка создания со слишком длинным описанием
     */
    public function testTooLongDescription()
    {
        $this->signIn();

        $response = $this->post(self::COMMON_URL, ['description' => $this->faker->text(1000)]);
        $response->assertSessionHasErrors(['description']);
    }

    /**
     * Успешное создание
     */
    public function testSuccess()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $community */
        $community = Community::factory()->make(['name' => 'Test Test', 'user_id' => $user->id]);

        $response = $this->post(self::COMMON_URL, $community->toArray());
        $response->assertRedirect(self::COMMON_URL);
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('communities', [
            'name'        => $community->name,
            'slug'        => 'test-test',
            'description' => $community->description,
            'user_id'     => $community->user_id,
        ]);
    }
}
