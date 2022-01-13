<?php

namespace App\Domain\Collections\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;
use App\Domain\Cards\DataObjects\CoreCardData;

class CollectionCardData extends CoreCardData implements DataObjectInterface
{
    public string $acquired_date;

    public int $acquired_price;

    public string $condition;

    public string $display_acquired_price;

    public string $display_price;

    public string $finish;

    public int $price;

    public int $quantity;

    public string $set;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->set                      = strtoupper($data['set'] ?? '');
        $this->price                    = $data['price'] ?? 0;
        $this->display_price            = $data['display_price'] ?? '';
        $this->acquired_date            = $data['acquired_date'] ?? '';
        $this->acquired_price           = $data['acquired_price'] ?? 0;
        $this->display_acquired_price   = $data['display_acquired_price'] ?? 0;
        $this->quantity                 = $data['quantity'] ?? 0;
        $this->finish                   = $data['finish'] ?? 'nonfoil';
        $this->condition                = $data['condition'] ?? '';
    }

    public function toArray() : array
    {
        return array_merge(
            parent::toArray(),
            [
                'id'                       => $this->id,
                'price'                    => $this->price,
                'display_price'            => $this->display_price,
                'acquired_date'            => $this->acquired_date,
                'acquired_price'           => $this->acquired_price,
                'display_acquired_price'   => $this->display_acquired_price,
                'quantity'                 => $this->quantity,
                'finish'                   => $this->finish,
                'condition'                => $this->condition,
                'set'                      => $this->set,
            ]);
    }
}
