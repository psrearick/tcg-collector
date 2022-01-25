<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;

class CollectionCardEventData implements DataObjectInterface
{
    public ?int $acquired;

    public string $card;

    public string $collection;

    public string $condition;

    public string $date_added;

    public string $finish;

    public ?int $from_acquired;

    public string $from_condition;

    public string $from_finish;

    public ?int $from_quantity;

    public ?int $price;

    public ?int $quantity;

    public ?int $updated_price;

    public function __construct(array $data)
    {
        $this->acquired         = (int) ($data['acquired'] ?? null);
        $this->card             = $data['card'] ?? '';
        $this->collection       = $data['collection'] ?? '';
        $this->condition        = $data['condition'] ?? '';
        $this->date_added       = $data['date_added'] ?? '';
        $this->finish           = $data['finish'] ?? '';
        $this->from_acquired    = (int) ($data['from_acquired'] ?? null);
        $this->from_condition   = $data['from_condition'] ?? '';
        $this->from_finish      = $data['from_finish'] ?? '';
        $this->from_quantity    = (int) ($data['from_quantity'] ?? null);
        $this->price            = (int) ($data['price'] ?? null);
        $this->quantity         = (int) ($data['quantity'] ?? null);
        $this->updated_price    = (int) ($data['updated_price'] ?? null);
    }

    public function toArray() : array
    {
        return [
            'acquired'          => $this->acquired,
            'card'              => $this->card,
            'collection'        => $this->collection,
            'condition'         => $this->condition,
            'date_added'        => $this->date_added,
            'finish'            => $this->finish,
            'from_acquired'     => $this->from_acquired,
            'from_condition'    => $this->from_condition,
            'from_finish'       => $this->from_finish,
            'from_quantity'     => $this->from_quantity,
            'price'             => $this->price,
            'quantity'          => $this->quantity,
            'updated_price'     => $this->updated_price,
        ];
    }

    public function toCollectionCard() : array
    {
        return [
            'collection'    => $this->collection,
            'card'          => $this->card,
            'values'        => [
                'price_when_added'  => $this->acquired,
                'condition'         => $this->condition,
                'date_added'        => $this->date_added,
                'finish'            => $this->finish,
                'quantity'          => $this->quantity,
            ],
        ];
    }

    public function toCollectionCardEvent() : array
    {
        return [
            'uuid'      => $this->collection,
            'change'    => [
                'id'                => $this->card,
                'finish'            => $this->finish,
                'change'            => $this->quantity,
                'condition'         => $this->condition,
                'acquired_price'    => $this->acquired,
            ],
        ];
    }

    public function toCollectionCardSummary() : array
    {
        return [
            'card_uuid'             => $this->card,
            'collection_uuid'       => $this->collection,
            'finish'                => $this->finish,
            'price_when_added'      => $this->acquired,
            'price_when_updated'    => $this->updated_price,
            'current_price'         => $this->price,
            'condition'             => $this->condition,
            'quantity'              => $this->quantity,
        ];
    }
}
