<?php

namespace App\Domain\Base;

use App\App\Contracts\DataObjectInterface;

abstract class SearchData implements DataObjectInterface
{
    public ?array $paginator;

    public function toArray() : array
    {
        return [
            'paginator' => $this->paginator,
        ];
    }
}