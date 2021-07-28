<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Проверка, является ли пользователь администратором
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function isAdmin(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            throw new Exception('Failed to find user');
        }

        return $user->hasRole(RoleEnum::ADMIN);
    }

    /**
     * Получение списка пользователей-администраторов
     *
     * @return Collection
     */
    public function getAdminUsers(): Collection
    {
        return User::whereHas("roles", function ($q) {
            $q->where("name", RoleEnum::ADMIN);
        })->get();
    }

    /**
     * Получение пользователя по ID
     *
     * @param int $userId ID пользователя, которого необходимо получить
     *
     * @return User|null
     */
    public function findUserById(int $userId): ?User
    {
        return User::whereId($userId)->first();
    }
}
