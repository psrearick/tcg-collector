<?php

namespace App\Domain\Sets\Models;

use App\App\Scopes\NotOnlineOnlySetScope;
use App\Domain\Base\Model;
use App\Domain\CardAttributes\Models\Printing;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Models\Token;
use Database\Factories\SetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Set extends Model
{
    use HasFactory;

    public static function booted() : void
    {
        static::addGlobalScope(new NotOnlineOnlySetScope);
    }

    /**
     * get all cards in this set
     *
     * @return HasMany
     */
    public function cards() : HasMany
    {
        return $this->hasMany(Card::class);
    }

    /**
     * return this cards printing record
     *
     * @return HasMany
     */
    public function printings() : HasMany
    {
        return $this->hasMany(Printing::class);
    }

    /**
     * get all tokens in this set
     *
     * @return HasMany
     */
    public function tokens() : HasMany
    {
        return $this->hasMany(Token::class);
    }

    protected static function newFactory()
    {
        return SetFactory::new();
    }
}
