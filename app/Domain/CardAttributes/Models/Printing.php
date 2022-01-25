<?php

namespace App\Domain\CardAttributes\Models;

use App\Domain\Base\Model;
use App\Domain\Sets\Models\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printing extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function Set() : BelongsTo
    {
        return $this->belongsTo(Set::class);
    }
}
