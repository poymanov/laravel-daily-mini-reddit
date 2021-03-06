<?php

namespace Tests;

use App\Enums\RoleEnum;
use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostImage;
use App\Models\PostVote;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

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
     * Создание пользователя с правами администратора
     *
     * @param array $params
     *
     * @return User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function createAdmin(array $params = [])
    {
        /** @var User $user */
        $user = User::factory($params)->create();
        $user->assignRole(RoleEnum::ADMIN);

        return $user;
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
     * Создание сущности PostImage
     *
     * @param array $params
     *
     * @return PostImage
     */
    protected function createPostImage(array $params = []): PostImage
    {
        return PostImage::factory()->create($params);
    }

    /**
     * Создание сущности PostVote
     *
     * @param array $params
     *
     * @return PostVote
     */
    protected function createPostVote(array $params = []): PostVote
    {
        return PostVote::factory()->create($params);
    }

    /**
     * Создание сущности PostComment
     *
     * @param array $params
     * @param bool  $isDeleted
     *
     * @return PostComment
     */
    protected function createPostComment(array $params = [], bool $isDeleted = false): PostComment
    {
        $factory = PostComment::factory();

        if ($isDeleted) {
            $factory = $factory->deleted();
        }

        return $factory->create($params);
    }

    /**
     * Создание сущности Report
     *
     * @param array $params
     * @param bool  $isDeleted
     *
     * @return Report
     */
    protected function createReport(array $params = [], bool $isDeleted = false): Report
    {
        $factory = Report::factory();

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
