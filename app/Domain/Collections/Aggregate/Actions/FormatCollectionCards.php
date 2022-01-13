<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Actions\PaginateSearchResults;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Support\Collection;
use Brick\Money\Money;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FormatCollectionCards
{
    public function __invoke(Collection $builder, CollectionCardSearchParameterData $collectionCardSearchParameterData) : LengthAwarePaginator
    {
        $builder->transform(function ($card) {
            $card->display_price = Money::ofMinor($card->price, 'USD')->formatTo('en_US');
            $card->display_acquired_price = Money::ofMinor($card->acquired_price, 'USD')->formatTo('en_US');

            return $card;
        });

        $needsGrouped = false;
        $settings     = auth()->user()->settings->first();
        if (optional($settings)->tracks_condition || optional($settings)->tracks_price) {
            $needsGrouped = true;
        }

        if ($needsGrouped) {
            $builder = $builder->mapToGroups(function ($group) {
                return [$group->uuid => $group];
            })
            ->map(function ($cardGroup) {
                return $cardGroup->mapToGroups(function ($group) {
                    return [$group->finish => $group];
                });
            })
            ->values();
        }

        return (new PaginateSearchResults())($builder, $collectionCardSearchParameterData);
    }
}
