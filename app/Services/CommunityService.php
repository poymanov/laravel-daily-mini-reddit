<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Community;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CommunityService
{
    /**
     * Получение списка всех сущностей
     *
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.profile_community');

        return Community::paginate($perPage);
    }

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

    /**
     * Получение ограниченного списка последних сообществ
     *
     * @param int|null $limit
     *
     * @return Collection
     */
    public function getAllLatestWithLimit(int $limit = null): Collection
    {
        $limit = $limit ?? config('custom.home_latest_communities_limit');

        return Community::latest()->limit($limit)->get();
    }
}
