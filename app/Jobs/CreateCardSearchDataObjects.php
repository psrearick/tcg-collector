<?php

namespace App\Jobs;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Models\CardSearchDataObject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class CreateCardSearchDataObjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Card $card;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Card $card)
    {
        $this->card = $card;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $card = $this->card;

        Redis::funnel('cardobject')->limit(1)->then(function () use ($card) {
            if ($card->isOnlineOnly) {
                return;
            }

            if (!$card->set) {
                return;
            }

            if (!$card->set->id) {
                return;
            }

            $prices = (new GetLatestPrices)([$card->uuid]);
            $prices->transform(function ($price) {
                $price->type = (new MatchType)($price->type);

                return $price;
            });
            $prices      = json_encode($prices->pluck('price', 'type')->toArray());
            $cardBuilder = new BuildCard($card);
            $build       = $cardBuilder
                        ->add('feature')
                        ->add('image_url')
                        ->add('set_image_url')
                        ->get();

            $finishes = json_encode($build->finishes->pluck('name')->toArray());

            CardSearchDataObject::create([
                'card_uuid'              => $build->uuid,
                'card_name'              => $build->name,
                'card_name_normalized'   => $build->name_normalized,
                'set_id'                 => optional($build->set)->id,
                'set_name'               => optional($build->set)->name,
                'set_code'               => optional($build->set)->code,
                'features'               => $build->feature,
                'prices'                 => $prices,
                'collector_number'       => $build->collectorNumber,
                'finishes'               => $finishes,
                'image'                  => $build->image_url,
                'set_image'              => $build->set_image_url,
            ]);
        }, function () {
            return $this->release(10);
        });
    }
}
