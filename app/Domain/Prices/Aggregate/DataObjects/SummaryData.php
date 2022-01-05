<?php

namespace App\Domain\Prices\Aggregate\DataObjects;

class SummaryData
{
    public string $object_uuid;

    public string $type;

    public ?int $total_cards;

    public float $current_value;

    public float $acquired_value;

    public float $gain_loss;

    public float $gain_loss_percent;


    public function __construct(array $data)
    {
        $this->object_uuid        = $data['uuid'] ?? '';
        $this->type             = $data['type'] ?? 'collection';
        $this->total_cards             = $data['total_cards'] ?? 0;
        $this->current_value            = $data['current_value'] ?? 0.0;
        $this->acquired_value            = $data['acquired_value'] ?? 0.0;
        $this->gain_loss            = $data['gain_loss'] ?? 0.0;
        $this->gain_loss_percent            = $data['gain_loss_percent'] ?? 0.0;
    }

    public function toArray() : array
    {
        return [
            'object_uuid'     => $this->object_uuid,
            'type'          => $this->type,
            'total_cards'          => $this->total_cards,
            'current_value'            => $this->current_value,
            'acquired_value'         => $this->acquired_value,
            'gail_loss'          => $this->gail_loss,
            'gain_loss_percent'         => $this->gain_loss_percent,
            'foil'          => $this->foil,
        ];
    }
}
