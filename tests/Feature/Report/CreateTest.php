<?php

declare(strict_types=1);

namespace Tests\Feature\Report;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Страница жалобы недоступна гостям
     */
    public function testGuest()
    {
        $response = $this->get($this->buildCreateUrl('post', 1));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Страница жалобы недоступна пользователям с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $this->signIn($this->createUser([], true));
        $response = $this->get($this->buildCreateUrl('post', 1));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Страница жалобы недоступна без параметров type и id
     */
    public function testWithoutParams()
    {
        $this->signIn();
        $response = $this->get($this->buildCreateUrl());
        $response->assertNotFound();
    }

    /**
     * Страница жалобы недоступна без параметра type
     */
    public function testWithoutTypeParam()
    {
        $this->signIn();
        $response = $this->get($this->buildCreateUrl(null, 1));
        $response->assertNotFound();
    }

    /**
     * Страница жалобы недоступна без параметра id
     */
    public function testWithoutTypeId()
    {
        $this->signIn();
        $response = $this->get($this->buildCreateUrl('post', null));
        $response->assertNotFound();
    }

    /**
     * Страница жалобы недоступна с неправильным type
     */
    public function testWrongTypeParamValue()
    {
        $this->signIn();
        $response = $this->get($this->buildCreateUrl('test', 1));
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Wrong report type');
    }

    /**
     * Страница жалобы недоступна для несуществующего по id объекта
     */
    public function testNotExistedObjectById()
    {
        $this->signIn();
        $response = $this->get($this->buildCreateUrl('post', 1));
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Not existed object for report');
    }

    /**
     * Страница жалобы недоступна для удаленного объекта
     */
    public function testDeletedObject()
    {
        $this->signIn();
        $post = $this->createPost([], true);

        $response = $this->get($this->buildCreateUrl('post', $post->id));
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Object for report was deleted');
    }

    /**
     * Страница жалобы недоступна для объекта, автором которого является текущий авторизованный пользователь
     */
    public function testObjectAuthor()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $post = $this->createPost(['user_id' => $user->id]);

        $response = $this->get($this->buildCreateUrl('post', $post->id));
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

        $this->createReport(['reportable_type' => Post::class, 'reportable_id' => $post->id]);

        $response = $this->get($this->buildCreateUrl('post', $post->id));
        $response->assertRedirect();
        $response->assertSessionHas('alert.error', 'Wrong object for report (already exists for user)');
    }

    /**
     * Страница успешно отображается
     */
    public function testSuccess()
    {
        $this->signIn();

        $post = $this->createPost();

        $type = 'post';
        $id   = $post->id;

        $response = $this->get($this->buildCreateUrl($type, $id));
        $response->assertOk();
        $response->assertSee('New Report');
        $response->assertSee('Report');
        $response->assertSee($type);
        $response->assertSee($id);
    }

    /**
     * Формирование пути страницы создания жалобы
     *
     * @param string|null $type
     * @param int|null    $id
     *
     * @return string
     */
    protected function buildCreateUrl(string $type = null, ?int $id = null): string
    {
        $baseUrl = '/reports/create';

        $params = [
            'type' => $type,
            'id'   => $id,
        ];

        return $baseUrl . '?' . http_build_query($params);
    }
}
