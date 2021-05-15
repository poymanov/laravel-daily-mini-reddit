<?php

declare(strict_types=1);

namespace Tests\Feature\Report;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public const STORE_URL = '/reports';

    /**
     * Создание недоступно гостям
     */
    public function testGuest()
    {
        $response = $this->post(self::STORE_URL);
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Создание недоступно пользователям с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $this->signIn($this->createUser([], true));
        $response = $this->post(self::STORE_URL);
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Создание недоступно без указания параметров
     */
    public function testEmpty()
    {
        $this->signIn();
        $response = $this->post(self::STORE_URL);
        $response->assertSessionHasErrors();
    }

    /**
     * Создание недоступно без параметра type
     */
    public function testWithoutType()
    {
        $data = [
            'type' => null,
            'id'   => 1,
            'text' => $this->faker->sentence(),
        ];

        $this->signIn();
        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasErrors(['type']);
    }

    /**
     * Создание недоступно без параметра id
     */
    public function testWithoutId()
    {
        $data = [
            'type' => 'post',
            'id'   => null,
            'text' => $this->faker->sentence(),
        ];

        $this->signIn();
        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasErrors(['id']);
    }

    /**
     * Длина текста жалобы не более 200 символов
     */
    public function testTooLongText()
    {
        $post = $this->createPost();

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => $this->faker->text(1000),
        ];

        $this->signIn();
        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasErrors(['text']);
    }

    /**
     * Создание недоступно без текста жалобы
     */
    public function testWithoutText()
    {
        $post = $this->createPost();

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => null,
        ];

        $this->signIn();
        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasErrors(['text']);
    }

    /**
     * Создание недоступно с неправильным type
     */
    public function testWrongTypeParamValue()
    {
        $this->signIn();

        $data = [
            'type' => 'test',
            'id'   => 1,
            'text' => $this->faker->sentence(),
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Wrong report type');
    }

    /**
     * Создание недоступно для несуществующего по id объекта
     */
    public function testNotExistedObjectById()
    {
        $this->signIn();

        $data = [
            'type' => 'post',
            'id'   => 1,
            'text' => $this->faker->sentence(),
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Not existed object for report');
    }

    /**
     * Создание недоступно для удаленного объекта
     */
    public function testDeletedObject()
    {
        $this->signIn();
        $post = $this->createPost([], true);

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => $this->faker->sentence(),
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Object for report was deleted');
    }

    /**
     * Создание недоступно для объекта, автором которого является текущий авторизованный пользователь
     */
    public function testObjectAuthor()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $post = $this->createPost(['user_id' => $user->id]);

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => $this->faker->sentence(),
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Wrong object for report (author)');
    }

    /**
     * Страница жалобы недоступна, если пользователь уже создавал для данного объекта жалобу
     */
    public function testAlreadyExists()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $post = $this->createPost();

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => $this->faker->sentence(),
        ];

        $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $response = $this->post(self::STORE_URL, array_merge($data));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Wrong object for report (already exists for user)');
    }

    /**
     * Успешное создание жалобы с типом "Комментарий"
     */
    public function testSuccessComment()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $comment = $this->createPostComment();

        $text = $this->faker->sentence();

        $data = [
            'type' => 'comment',
            'id'   => $comment->id,
            'text' => $text,
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.success', 'Report was successfully created');

        $this->assertDatabaseHas('reports', [
            'user_id'         => $user->id,
            'text'            => $text,
            'reportable_type' => PostComment::class,
            'reportable_id'   => $comment->id,
        ]);
    }

    /**
     * Успешное создание жалобы с типом "Публикация"
     */
    public function testSuccessPost()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $post = $this->createPost();

        $text = $this->faker->sentence();

        $data = [
            'type' => 'post',
            'id'   => $post->id,
            'text' => $text,
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.success', 'Report was successfully created');

        $this->assertDatabaseHas('reports', [
            'user_id'         => $user->id,
            'text'            => $text,
            'reportable_type' => Post::class,
            'reportable_id'   => $post->id,
        ]);
    }

    /**
     * Успешное создание жалобы с типом "Сообщество"
     */
    public function testSuccessCommunity()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();

        $text = $this->faker->sentence();

        $data = [
            'type' => 'community',
            'id'   => $community->id,
            'text' => $text,
        ];

        $response = $this->post(self::STORE_URL, $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $response->assertSessionHas('alert.success', 'Report was successfully created');

        $this->assertDatabaseHas('reports', [
            'user_id'         => $user->id,
            'text'            => $text,
            'reportable_type' => Community::class,
            'reportable_id'   => $community->id,
        ]);
    }
}
