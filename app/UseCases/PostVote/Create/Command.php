<?php

declare(strict_types=1);

namespace App\UseCases\PostVote\Create;

class Command
{
    /** @var int */
    public int $postId;

    /** @var int */
    public int $communityId;

    /** @var int */
    public int $userId;

    /** @var int */
    public int $vote;
}
