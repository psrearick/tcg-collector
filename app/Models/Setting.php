<?php

namespace App\Models;

use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
