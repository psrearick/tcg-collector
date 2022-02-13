<?php

namespace App\Domain\Cards\DataObjects;

use App\App\Contracts\DataObjectInterface;
use Illuminate\Support\Facades\Log;
use JsonException;

abstract class CoreCardData implements DataObjectInterface
{
    public string $collector_number;

    public string $features;

    public array $finishes;

    public int $id;

    public string $image;

    public string $name;

    public string $name_normalized;

    public array $prices;

    public string $set_code;

    public string $set_id;

    public string $set_image;

    public string $set_name;

    public string $uuid;

    public function __construct(array $data)
    {
        $this->collector_number = $data['collector_number'] ?? '';
        $this->features         = $data['features'] ?? '';
        $this->finishes         = $this->getArrayValue($data['finishes'] ?? []);
        $this->id               = $data['id'] ?? null;
        $this->image            = $data['image'] ?? '';
        $this->name             = $data['name'] ?? '';
        $this->name_normalized  = $data['name_normalized'] ?? '';
        $this->prices           = $this->getArrayValue($data['prices'] ?? [], true);
        $this->set_id           = $data['set_id'] ?? '';
        $this->set_code         = strtoupper($data['set_code'] ?? '');
        $this->set_image        = $data['set_image'] ?? '';
        $this->set_name         = $data['set_name'] ?? '';
        $this->uuid             = $data['uuid'] ?? '';
    }

    public function toArray() : array
    {
        return [
            'collector_number' => $this->collector_number,
            'features'         => $this->features,
            'finishes'         => $this->finishes,
            'id'               => $this->id,
            'image'            => $this->image,
            'name'             => $this->name,
            'name_normalized'  => $this->name_normalized,
            'prices'           => $this->prices,
            'set_code'         => $this->set_code,
            'set_image'        => $this->set_image,
            'set_name'         => $this->set_name,
            'uuid'             => $this->uuid,
        ];
    }

    private function getArrayValue(mixed $value, bool $associative = false) : array
    {
        if (is_string($value)) {
            try {
                $value = json_decode($value, $associative, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                Log::alert($exception);

                return [];
            }
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }
}
