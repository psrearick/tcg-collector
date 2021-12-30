<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\DataObjects\CardData;
use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class FormatCards
{
    public function __invoke(Builder $builder)
    {
        return (new Collection($builder->get()))
            ->map(function ($model) {
            $cardBuilder = new BuildCard($model);
            $card = $cardBuilder
                ->add('feature')
                ->add('allPrices')
                ->add('image_url')
                ->add('set_image_url')
                ->get()
                ->toArray();
            
            return (new CardData([
                'id'        => $card['id'],
                'uuid'      => $card['uuid'],
                'name'      => $card['name'],
                'set_code'  => $card['set']['code'] ?? '',
                'set_name'  => $card['set']['name'] ?? '',
                'collected' => [],
                'prices'    => $card['allPrices'],
                'quantities'    => [],
                'features'      => $card['feature'],
                'finishes'      => $card['finishes'],
                'image'         => $card['image_url'],
                'set_image'     => $card['set_image_url'],
                'collector_number'  => $card['collector_number'] ?? '',
            ]))->toArray();
        });
    }
}