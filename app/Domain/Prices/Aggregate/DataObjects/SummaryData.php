<?php

namespace App\Domain\Prices\Aggregate\DataObjects;

use App\App\Contracts\DataObjectInterface;

class SummaryData implements DataObjectInterface
{
    public int $acquired_value;

    public int $current_value;

    public string $display_acquired_value;

    public string $display_current_value;

    public string $display_gain_loss;

    public int $gain_loss;

    public float $gain_loss_percent;

    public string $object_uuid;

    public ?int $total_cards;

    public string $type;

    public function __construct(array $data)
    {
        $this->object_uuid              = $data['uuid'] ?? '';
        $this->type                     = $data['type'] ?? 'collection';
        $this->total_cards              = $data['total_cards'] ?? 0;
        $this->current_value            = $data['current_value'] ?? 0;
        $this->display_current_value    = $data['display_current_value'] ?? 0;
        $this->acquired_value           = $data['acquired_value'] ?? 0;
        $this->display_acquired_value   = $data['display_acquired_value'] ?? 0;
        $this->gain_loss                = $data['gain_loss'] ?? 0;
        $this->display_gain_loss        = $data['display_gain_loss'] ?? 0;
        $this->gain_loss_percent        = $data['gain_loss_percent'] ?? 0.0;
    }

    public function toArray() : array
    {
        return [
            'object_uuid'               => $this->object_uuid,
            'type'                      => $this->type,
            'total_cards'               => $this->total_cards,
            'current_value'             => $this->current_value,
            'current_current_value'     => $this->current_current_value,
            'acquired_value'            => $this->acquired_value,
            'current_acquired_value'    => $this->current_acquired_value,
            'gain_loss'                 => $this->gain_loss,
            'current_gain_loss'         => $this->current_gain_loss,
            'gain_loss_percent'         => $this->gain_loss_percent,
        ];
    }
}
