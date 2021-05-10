<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use Exception;

class UserService
{
    /**
     * Проверка, является ли пользователь администратором
     *
     * @param int $id
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
}
