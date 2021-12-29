<?php

namespace App\Domain\Collections\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Base\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Collection extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
