<?php

namespace App\Domain\Cards\Presenters;

use App\App\Contracts\PresenterInterface;
use App\App\Contracts\PresentsLegalities;
use App\App\Contracts\PresentsPrices;
use App\App\Contracts\PresentsPrintings;
use App\Domain\Base\Collection;
use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Actions\GetPrintings;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection as ModelCollection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Groups\Actions\GetGroupCollectionUuids;
use App\Domain\Prices\Aggregate\Actions\GetLatestPricesByFinish;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

class CardShowPresenter implements PresenterInterface, PresentsPrices, PresentsLegalities, PresentsPrintings
{
    private Card $card;

    private SupportCollection $printings;

    public function __construct(string $uuid)
    {
        $this->card = optional(Card::uuid($uuid))
            ->load('set', 'finishes');
    }

    public function getLegalities() : array
    {
        return $this->card->legalities
            ->map(function ($legality) {
                return [
                    'format'    => Str::headline($legality->format),
                    'status'    => Str::headline($legality->status),
                ];
            })
            ->sortBy('format')
            ->keyBy('format')
            ->map(fn ($legality) => $legality['status'])
            ->toArray();
    }

    public function getPrices() : array
    {
        return (new GetLatestPricesByFinish)($this->card->uuid);
    }

    public function getPrintingsPresent() : array
    {
        return (new PrintingsPresenter($this->card->oracleId))->present();
    }

    public function present() : array
    {
        $prices = $this->getPrices();
        $this->setPrintings();

        $card = (new BuildCard($this->card))
            ->add('feature')
            ->add('image_url')
            ->add('set_image_url')
            ->get();

        return [
            'name'              => $card->name,
            'set_name'          => $card->set->name,
            'uuid'              => $card['uuid'],
            'set_code'          => $card->set->code ?? '',
            'prices'            => $prices ?? [],
            'features'          => $card['feature'],
            'image'             => $card['image_url'],
            'set_image'         => $card['set_image_url'],
            'collector_number'  => $card['collectorNumber'] ?? '',
            'legalities'        => $this->getLegalities(),
            'rarity'            => Str::headline($card->rarity),
            'type'              => $card->typeLine,
            'mana_cost'         => $card->manaCost,
            'oracle_text'       => $card->oracleText,
            'language'          => Str::upper($card->languageCode),
            'artist'            => $card->artist,
            'printings'         => $this->getPrintingsPresent(),
            'collections'       => $this->getCollections(),
            'group_collections' => $this->getGroupCollections(),
        ];
    }

    public function setPrintings() : void
    {
        $this->printings = (new GetPrintings)($this->card->oracleId);
    }

//    private function

    private function getCollections() : array
    {
        $userCollections = ModelCollection::all();

        return $this->getCollectionsTotals($userCollections);
    }

    private function getCollectionsTotals(SupportCollection $collectionGroups) : array
    {
        return $collectionGroups
            ->unique('uuid')
            ->map(function (Collection $collection) {
                $data = $collection->only('uuid', 'name', 'description');
                $quantities = [
                    'nonfoil' => 0,
                    'foil'    => 0,
                    'etched'  => 0,
                    'total'   => 0,
                ];

                $collection->cardSummaries
                    ->whereIn('card_uuid', $this->printings->pluck('uuid'))
                    ->where('quantity', '>', 0)
                    ->whereNull('deleted_at')
                    ->mapToGroups(function (CollectionCardSummary $summary) {
                        return [$summary->finish => $summary->quantity];
                    })
                    ->each(function (SupportCollection $conditionQuantity, string $key) use (&$quantities) {
                        $quantity = $conditionQuantity->reduce(function ($quantity, $carry) {
                            return $quantity + ($carry ?: 0);
                        });

                        $quantities[$key] = $quantity;
                        $quantities['total'] += $quantity;
                    });

                $data['quantities'] = $quantities;

                return $data;
            })
            ->values()
            ->filter(function ($total) {
                return $total['quantities']['total'] > 0;
            })
            ->toArray();
    }

    private function getGroupCollections() : array
    {
        $request = optional(request())->merge(['inGroup' => true]);
        $totals  = $this->getTotals((new GetGroupCollectionUuids)());
        $request->merge(['inGroup' => false]);

        return collect($totals)->transform(function ($total) {
            $collection = optional(Collection::uuid($total['uuid']))->load('user');
            if (!$collection) {
                return true;
            }

            $total['owner'] = $collection->user->id === auth()->id()
                ? 'You'
                : $collection->user->name;

            return $total;
        })->toArray();
    }

    private function getTotals(array $collections) : array
    {
        $matches = (new GetPrintings)($this->card->oracleId)
            ->reduce(function (array $carry, Card $printing) use ($collections) {
                $printingCollections = $printing->collectionsGeneral->whereIn('uuid', $collections);
                $printingCollections->each(function ($collection) use (&$carry) {
                    $carry[$collection['uuid']] = $collection;
                });

                return $carry;
            }, []);

        return $this->getCollectionsTotals(collect($matches)->values());
    }
}
