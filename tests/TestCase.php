<?php

namespace Tests;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const LOGIN_URL = '/login';

    protected const VERIFY_EMAIL_URL = '/verify-email';

    /**
     * @param null $user
     */
    protected function signIn($user = null): void
    {
        $user = $user ?: User::factory()->create();
        $this->actingAs($user);
    }

    /**
     * Создание сущности User
     *
     * @param array $params
     * @param bool  $isUnverified
     *
     * @return User
     */
    protected function createUser(array $params = [], bool $isUnverified = false): User
    {
        $factory = User::factory();

        if ($isUnverified) {
            $factory = $factory->unverified();
        }

        return $factory->create($params);
    }

    /**
     * Создание сущности Community
     *
     * @param array $params
     * @param bool  $isDeleted
     *
     * @return Community
     */
    protected function createCommunity(array $params = [], bool $isDeleted = false): Community
    {
        $factory = Community::factory();

        if ($isDeleted) {
            $factory = $factory->deleted();
        }

        return $factory->create($params);
    }

    /**
     * Создание сущности Post
     *
     * @param array $params
     * @param bool  $isDeleted
     *
     * @return Post
     */
    protected function createPost(array $params = [], bool $isDeleted = false): Post
    {
        $factory = Post::factory();

        if ($isDeleted) {
            $factory = $factory->deleted();
        }

        return $factory->create($params);
    }

    /**
     * Подготовка сущности Post
     *
     * @param array $params
     *
     * @return Post
     */
    protected function makePost(array $params = []): Post
    {
        return Post::factory()->make($params);
    }
}
