<?php

namespace App\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $team_id
 * @property int|null $tracks_condition
 * @property int|null $tracks_price
 * @property int|null $expanded_default_show
 * @property int|null $expanded_default_edit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $team
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\SettingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereExpandedDefaultEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereExpandedDefaultShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTracksCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTracksPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUserId($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory;

    public function team() : BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
