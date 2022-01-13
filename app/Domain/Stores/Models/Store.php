<?php

namespace App\Domain\Stores\Models;

use App\Domain\Base\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class)
                        ->withPivot('role')
                        ->withTimestamps();
    }
}
