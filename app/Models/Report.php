<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Report
 *
 * @property int                             $id
 * @property string                          $text
 * @property int                             $user_id
 * @property string                          $reportable_type
 * @property int                             $reportable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Query\Builder|Report onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Report withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Report withoutTrashed()
 * @mixin \Eloquent
 * @method static \Database\Factories\ReportFactory factory(...$parameters)
 * @property-read Model|\Eloquent            $reportable
 * @property-read \App\Models\User           $user
 */
class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @return string
     * @throws Exception
     */
    public function getTypeLabel(): string
    {
        switch ($this->reportable_type) {
            case PostComment::class:
                return 'Comment';
            case Post::class:
                return 'Post';
            case Community::class:
                return 'Community';
            default:
                throw new Exception('Wrong report type');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'user_id' => 'integer',
    ];
}
