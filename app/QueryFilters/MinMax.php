<?php

namespace App\QueryFilters;

use App\App\Contracts\QueryFilterInterface;
use Brick\Money\Money;
use Illuminate\Support\Collection;

class MinMax implements QueryFilterInterface
{
    public function query(Collection $builder, array $parameters) : Collection
    {
        $values = $parameters['value'] ?? [];
        $field  = $parameters['field'] ?? '';

        if (isset($values['min'])) {
            if ($field == 'price' || $field == 'acquired_price') {
                $values['min'] = Money::of($values['min'], 'USD')->getMinorAmount()->toInt();
            }
            $builder = $builder->where($field, '>', $values['min']);
        }

        if (isset($values['max'])) {
            if ($field == 'price' || $field == 'acquired_price') {
                $values['max'] = Money::of($values['max'], 'USD')->getMinorAmount()->toInt();
            }
            $builder = $builder->where($field, '<', $values['max']);
        }

        return $builder;
    }
}
