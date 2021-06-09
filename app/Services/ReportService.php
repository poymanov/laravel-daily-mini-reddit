<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ReportTypeEnum;
use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Report;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportService
{
    public const TYPE_MAPPING = [
        ReportTypeEnum::COMMENT   => PostComment::class,
        ReportTypeEnum::POST      => Post::class,
        ReportTypeEnum::COMMUNITY => Community::class,
    ];

    /**
     * Получение списка всех жалоб на пользовательский контент
     *
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.profile_reports');

        return Report::latest()->paginate($perPage);
    }

    /**
     * Проверка существования типа жалобы
     *
     * @param string $type
     *
     * @throws Exception
     */
    public function checkType(string $type): void
    {
        if (!in_array($type, ReportTypeEnum::LIST)) {
            throw new Exception('Wrong report type');
        }
    }

    /**
     * Получение значения класса жалобы по простому представлению типа
     *
     * @param string $type
     *
     * @return string
     * @throws Exception
     */
    public function getReportableTypeByType(string $type): string
    {
        $this->checkType($type);

        return self::TYPE_MAPPING[$type];
    }

    /**
     * Проверка типа объекта для жалобы
     *
     * @param string $type
     * @param int    $id
     * @param int    $userId
     *
     * @throws Exception
     */
    public function checkReportObject(string $type, int $id, int $userId): void
    {
        $this->checkType($type);

        $objectClass = self::TYPE_MAPPING[$type];

        switch ($type) {
            case ReportTypeEnum::COMMENT:
                $object = PostComment::withTrashed()->find($id);
                break;
            case ReportTypeEnum::POST:
                $object = Post::withTrashed()->find($id);
                break;
            case ReportTypeEnum::COMMUNITY:
                $object = Community::withTrashed()->find($id);
                break;
            default:
                $object = null;
        }

        if (is_null($object)) {
            throw new Exception('Not existed object for report');
        }

        if ($object->trashed()) {
            throw new Exception('Object for report was deleted');
        }

        if ($object->user_id == $userId) {
            throw new Exception('Wrong object for report (author)');
        }

        if (Report::where('reportable_type', $objectClass)->where('reportable_id', $id)->exists()) {
            throw new Exception('Wrong object for report (already exists for user)');
        }
    }
}
