<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PostVote
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int $vote
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PostVoteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostVote whereVote($value)
 * @mixin \Eloquent
 */
class PostVote extends Model
{
    use HasFactory;
}
