<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UpdateTest extends CommunityTestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Форма редактирования недоступна для гостей
     */
    public function testUpdateScreenCannotBeRenderedForGuest()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->get($this->buildEditUrl($community->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Форма редактирования недоступна для пользователей с неподтвержденным email
     */
    public function testUpdateScreenCannotBeRenderedForNotVerifiedUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->get($this->buildEditUrl($community->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Форма редактирования сущности, созданной другим пользователем, недоступна
     */
    public function testUpdateScreenCannotBeRenderedForAnotherUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn();
        $response = $this->get($this->buildEditUrl($community->slug));

        $response->assertForbidden();
    }

    /**
     * Форма редактирования отображается
     */
    public function testUpdateScreenCanBeRendered()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $community */
        $community = Community::factory()->create(['user_id' => $user->id]);

        $response = $this->get($this->buildEditUrl($community->slug));
        $response->assertOk();

        $response->assertSee('Name');
        $response->assertSee('Description');
        $response->assertSee('Update');

        $response->assertSee($community->name);
        $response->assertSee($community->description);
    }

    /**
     * Попытка изменения с пустыми данными
     */
    public function testEmpty()
    {
        $this->signIn();

        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->patch($this->buildUpdateUrl($community->slug));
        $response->assertSessionHasErrors(['name', 'description']);
    }

    /**
     * Попытка редактирования со слишком коротким наименованием
     */
    public function testTooShortName()
    {
        $this->signIn();

        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->patch($this->buildUpdateUrl($community->slug), ['name' => '12']);
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Попытка редактирования с уже существующим наименованием
     */
    public function testNotUniqueName()
    {
        $this->signIn();

        /** @var Community $community */
        $community = Community::factory()->create();

        /** @var Community $anotherCommunity */
        $anotherCommunity = Community::factory()->create();

        $response = $this->patch($this->buildUpdateUrl($community->slug), ['name' => $anotherCommunity->name]);
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * Попытка редактирования со слишком длинным описанием
     */
    public function testTooLongDescription()
    {
        $this->signIn();

        /** @var Community $community */
        $community = Community::factory()->create();

        $response = $this->patch($this->buildUpdateUrl($community->slug), ['description' => $this->faker->text(1000)]);
        $response->assertSessionHasErrors(['description']);
    }

    /**
     * Попытка редактирования сущности, созданной другим пользователем
     */
    public function testUpdateAnotherUser()
    {
        /** @var Community $community */
        $community = Community::factory()->create();

        $this->signIn();
        $response = $this->patch($this->buildUpdateUrl($community->slug), [
            'name'        => $this->faker->sentence,
            'description' => $this->faker->text(50),
        ]);

        $response->assertForbidden();
    }

    /**
     * Успешное редактирование
     */
    public function testSuccess()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $community */
        $community = Community::factory()->create(['user_id' => $user->id]);

        $newName        = 'test-test';
        $newDescription = $this->faker->text(50);

        $response = $this->patch($this->buildUpdateUrl($community->slug), [
            'name'        => $newName,
            'description' => $newDescription,
        ]);

        $response->assertRedirect('/profile/communities');
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('communities', [
            'id'          => $community->id,
            'name'        => $newName,
            'slug'        => 'test-test',
            'description' => $newDescription,
        ]);
    }

    /**
     * Успешное редактирование без изменения собственного имени
     */
    public function testSuccessWithSameName()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->signIn($user);

        /** @var Community $community */
        $community = Community::factory()->create(['user_id' => $user->id]);

        $response = $this->patch($this->buildUpdateUrl($community->slug), [
            'name'        => $community->name,
            'description' => $this->faker->text(50),
        ]);

        $response->assertSessionHasNoErrors();
    }
}
