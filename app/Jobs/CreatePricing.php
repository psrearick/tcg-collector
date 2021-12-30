<?php

namespace App\Jobs;

use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\createPrice;
use App\Domain\Prices\Aggregate\Actions\createPriceProvider;
use App\Domain\Prices\Aggregate\DataObjects\PriceData;
use App\Domain\Prices\Models\PriceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePricing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Card $card;

    private array $cardData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $cardData, ?Card $card)
    {
        $this->cardData = $cardData;
        $this->card     = $card;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$prices = $this->cardData['prices']) {
            return;
        }

        $createPriceProvider = new createPriceProvider;
        $provider = $createPriceProvider(['name' => 'scryfall']);

        $card     = $this->card ?: Card::where('cardId', '=', $this->cardData['id'])->first();

        if (!$card) {
            return;
        }

        foreach ($prices as $type => $price) {
            $createPrice = new createPrice;
            $data = [
                'card_uuid'     => $card->uuid,
                'provider_uuid' => $provider,
                'price'         => $price,
                'foil'          => $type == 'usd_foil' || $type == 'usd_etched',
                'type'          => $type,
            ];

            $createPrice($data);
        }
    }
}
