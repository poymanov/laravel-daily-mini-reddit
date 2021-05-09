<?php

declare(strict_types=1);

namespace App\Enums;

use Exception;

final class RoleEnum
{
    public const ADMIN = 'admin';

    /**
     * RoleEnum constructor.
     */
    public function __construct()
    {
        throw new Exception();
    }
}
