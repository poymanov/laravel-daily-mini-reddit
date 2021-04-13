<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property string|null                     $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Community onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Community withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Community withoutTrashed()
 * @property string $slug
 * @method static \Illuminate\Database\Eloquent\Builder|Community findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Community whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Community withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
class Community extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sluggable;

    /**
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
