<?php

namespace App\Actions;

use App\Jobs\ChunkCardObjectCreate;

class CreateCardObjects
{
    public function __invoke()
    {
        ChunkCardObjectCreate::dispatch();
    }
}
