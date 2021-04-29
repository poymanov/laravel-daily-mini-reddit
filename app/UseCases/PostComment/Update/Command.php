<?php

declare(strict_types=1);

namespace App\UseCases\PostComment\Update;

class Command
{
    /** @var int */
    public int $postId;

    /** @var int */
    public int $commentId;

    /** @var int */
    public int $userId;

    /** @var string */
    public string $text;
}
