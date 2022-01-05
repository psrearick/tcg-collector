<?php

namespace App\App\Contracts;

use Illuminate\Support\Collection;

interface QueryFilterInterface
{
    public function query(Collection $builder, array $parameters) : Collection;
}
