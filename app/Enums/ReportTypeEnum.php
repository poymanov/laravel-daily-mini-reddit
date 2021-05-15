<?php

declare(strict_types=1);

namespace App\Enums;

use Exception;

final class ReportTypeEnum
{
    public const COMMENT   = 'comment';
    public const POST      = 'post';
    public const COMMUNITY = 'community';

    public const LIST      = [
        self::COMMENT,
        self::POST,
        self::COMMUNITY,
    ];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        throw new Exception();
    }
}
