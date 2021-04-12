<?php

declare(strict_types=1);

namespace App\UseCases\Community\Update;

class Command
{
    /** @var string */
    public string $name;

    /** @var string */
    public string $description;

    /** @var int */
    public int $id;
}
