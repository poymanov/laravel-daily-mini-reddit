<?php

declare(strict_types=1);

namespace App\UseCases\Report\Create;

class Command
{
    public string $text;

    public string $type;

    public int $id;

    public int $userId;
}
