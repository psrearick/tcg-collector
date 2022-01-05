<?php

namespace App\QueryFilters;

use App\App\Contracts\QueryFilterInterface;
use Illuminate\Support\Collection;

class MinMax implements QueryFilterInterface
{
    public function query(Collection $builder, array $parameters) : Collection
    {
        $values = $parameters['value'];
        $field  = $parameters['field'];

        if (isset($values['min'])) {
            $builder = $builder->where($field, '>', $values['min']);
        }

        if (isset($values['max'])) {
            $builder = $builder->where($field, '<', $values['max']);
        }

        return $builder;
    }
}
