<?php

declare(strict_types=1);

namespace App\UseCases\Post\Create;

class Command
{
    /** @var int */
    public int $communityId;

    /** @var int */
    public int $userId;

    /** @var string */
    public string $title;

    /** @var string|null */
    public ?string $text;

    /** @var string|null */
    public ?string $url;
}
