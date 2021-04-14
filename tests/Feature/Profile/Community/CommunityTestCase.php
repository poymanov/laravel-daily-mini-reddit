<?php

declare(strict_types=1);

namespace Tests\Feature\Profile\Community;

use Tests\TestCase;

abstract class CommunityTestCase extends TestCase
{
    protected const COMMON_URL = '/profile/communities';

    /**
     * Формирование пути для создания сущности
     *
     * @return string
     */
    protected function buildCreateUrl(): string
    {
        return self::COMMON_URL . '/create';
    }

    /**
     * Формирование адреса для удаления сущности
     *
     * @param string $slug
     *
     * @return string
     */
    protected function buildDeleteUrl(string $slug): string
    {
        return self::COMMON_URL . '/' . $slug;
    }

    /**
     * Формирование адреса редактирования сущности
     *
     * @param string $slug
     *
     * @return string
     */
    protected function buildEditUrl(string $slug): string
    {
        return self::COMMON_URL . '/' . $slug . '/edit';
    }

    /**
     * Формирование адреса внесения изменений в сущность
     *
     * @param string $slug
     *
     * @return string
     */
    protected function buildUpdateUrl(string $slug): string
    {
        return self::COMMON_URL . '/' . $slug;
    }
}
