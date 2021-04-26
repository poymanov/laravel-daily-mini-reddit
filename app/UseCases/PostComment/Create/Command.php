<?php

declare(strict_types=1);

namespace App\UseCases\PostComment\Create;

class Command
{
    public int $postId;

    public int $userId;

    public string $text;
}
