<?php

namespace App\Domain\Symbols\Models;

use App\Domain\Base\Model;
use App\Domain\CardAttributes\Models\Color;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Domain\Symbols\Models\Symbol
 *
 * @property int $id
 * @property string $symbol
 * @property string|null $svgUri
 * @property string|null $svgPath
 * @property string|null $looseVariant
 * @property string|null $english
 * @property int|null $transpose
 * @property int|null $representsMana
 * @property int|null $appearsInManaCosts
 * @property string|null $cmc
 * @property int|null $funny
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Color[] $colors
 * @property-read int|null $colors_count
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol query()
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereAppearsInManaCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereCmc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereEnglish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereFunny($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereLooseVariant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereRepresentsMana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereSvgPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereSvgUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereTranspose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Symbol whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Symbol extends Model
{
    public function colors() : BelongsToMany
    {
        return $this->belongsToMany(Color::class);
    }
}
