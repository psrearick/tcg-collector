<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\CardAttributes\Models\LeadershipSkill
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Card[] $cards
 * @property-read int|null $cards_count
 * @method static \Illuminate\Database\Eloquent\Builder|LeadershipSkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadershipSkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadershipSkill query()
 * @mixin \Eloquent
 */
class LeadershipSkill extends Model
{
    use HasFactory;

    // get all cards assigned to this leadership skill
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
