<?php

declare(strict_types=1);

namespace App\UseCases\PostComment\Delete;

class Command
{
    public int $postId;

    public int $commentId;

    public int $userId;
}
