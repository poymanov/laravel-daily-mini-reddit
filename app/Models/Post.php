<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

/**
 * App\Models\Post
 *
 * @method static \Database\Factories\PostFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Post findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Query\Builder|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Query\Builder|Post withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Post withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Query\Builder|Post withoutTrashed()
 * @mixin \Eloquent
 * @property int                                                                   $id
 * @property int                                                                   $community_id
 * @property int                                                                   $user_id
 * @property string                                                                $title
 * @property string|null                                                           $text
 * @property string|null                                                           $url
 * @property string                                                                $slug
 * @property \Illuminate\Support\Carbon|null                                       $created_at
 * @property \Illuminate\Support\Carbon|null                                       $updated_at
 * @property \Illuminate\Support\Carbon|null                                       $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCommunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @property-read \App\Models\Community                                            $community
 * @property-read string|null                                                      $large_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostImage[] $largeImage
 * @property-read int|null                                                         $large_image_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostVote[] $votes
 * @property-read int|null $votes_count
 * @property-read int $rating
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostComment[] $comments
 * @property-read int|null $comments_count
 */
class Post extends Model
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
                'source' => 'title',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany(PostVote::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function largeImage()
    {
        return $this->hasMany(PostImage::class)->where('type', 'large');
    }

    /**
     * @return string|null
     */
    public function getLargeImageUrlAttribute(): ?string
    {
        /** @var PostImage $largeImage */
        $largeImage = $this->largeImage->first();

        if (isset($largeImage->name)) {
            return Storage::disk('public')->url('posts/' . $this->id . '/' . $largeImage->name);
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getRatingAttribute(): int
    {
        return $this->votes()->sum('vote');
    }
}
