<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Community;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommunityService
{
    /**
     * Получение списка всех сущностей пользователя
     *
     * @param int      $userId
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllByUserId(int $userId, int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.profile_community');

        return Community::where(['user_id' => $userId])->paginate($perPage);
    }

    /**
     * Отображение всех последних записей
     *
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllLatest(int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.community');

        return Community::latest()->paginate($perPage);
    }
}
