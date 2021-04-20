<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PostImage
 *
 * @property int $id
 * @property int $post_id
 * @property string $type
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|PostImage whereName($value)
 */
class PostImage extends Model
{
    use HasFactory;
}
