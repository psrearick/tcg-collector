<?php

namespace App\Jobs;

use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\CreatePriceProvider;
use App\Domain\Prices\Models\Price;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateAndPurgePricing implements ShouldQueue
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

    public function handle() : void
    {
        CreatePricing::dispatch($this->cardData, $this->card);

        if (!$prices = $this->cardData['prices']) {
            return;
        }

        $createPriceProvider = new CreatePriceProvider;
        $provider            = $createPriceProvider(['name' => 'scryfall']);

        $card     = $this->card ?: Card::where('cardId', '=', $this->cardData['id'])->first();

        if (!$card) {
            return;
        }

        foreach ($prices as $type => $price) {
            Price::query()
                ->where('card_uuid', '=', $card->uuid)
                ->where('provider_uuid', '=', $provider)
                ->where('type', '=', $type)
                ->whereDate('created_at', '<', Carbon::now())
                ->delete();
        }
    }
}
