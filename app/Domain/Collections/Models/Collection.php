<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Collection as BaseCollection;
use App\Traits\BelongsToUserScoped;

class Collection extends BaseCollection
{
    use BelongsToUserScoped;

    const USERSCOPE = 'notShared';
}
