<?php

namespace App\Domain\Cards\Models;

use App\Domain\Base\Model;
use App\Domain\CardAttributes\Models\Subtype;
use App\Domain\CardAttributes\Models\Supertype;
use App\Domain\CardAttributes\Models\Type;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Domain\Cards\Models\CardGeneric
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CardGeneric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardGeneric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardGeneric query()
 * @mixin \Eloquent
 */
class CardGeneric extends Model
{
//    /**
//     * get all subtypes for this card
//     *
//     * @return MorphToMany
//     */
//    public function subtypes() : MorphToMany
//    {
//        return $this->morphToMany(Subtype::class, 'subtypeable');
//    }
//
//    /**
//     * get all supertypes for this card
//     *
//     * @return MorphToMany
//     */
//    public function supertypes() : MorphToMany
//    {
//        return $this->morphToMany(Supertype::class, 'supertypeable');
//    }
//
//    /**
//     * get all of this card's types
//     *
//     * @return MorphToMany
//     */
//    public function types() : MorphToMany
//    {
//        return $this->morphToMany(Type::class, 'typeable');
//    }
}
