<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Community;
use Illuminate\Database\Eloquent\Collection;

class CommunityService
{
    /**
     * Получение списка всех сущностей пользователя
     *
     * @param int $userId
     *
     * @return Collection
     */
    public function getAllByUserId(int $userId): Collection
    {
        return Community::where(['user_id' => $userId])->get();
    }
}
