<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Finish extends Model
{
    use HasFactory;
    
    public function cards() : BelongsToMany
    {
        return $this->belongsToMany(Card::class);
    }
}
