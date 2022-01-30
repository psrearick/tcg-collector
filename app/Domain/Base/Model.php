<?php

namespace App\Domain\Base;

use Eloquent;

/**
 * App\Domain\Base\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 * @mixin Eloquent
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    public const SCOPE = '';

    protected $guarded = ['id'];
}
