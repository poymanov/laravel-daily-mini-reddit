<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Community
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $description
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Community newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Community newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Community query()
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereUserId($value)
 * @mixin \Eloquent
 * @method static \Database\Factories\CommunityFactory factory(...$parameters)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereDeletedAt($value)
 */
class Community extends Model
{
    use HasFactory;
}
