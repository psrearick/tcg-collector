<?php

namespace App\Jobs;

use App\Actions\NormalizeString;
use App\Domain\Cards\Models\Card;
use App\Domain\Sets\Models\Set;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UpdateCard implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $cardData;

    private array $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $cardData, array $options)
    {
        $this->cardData = $cardData;
        $this->options  = $options;
    }

    public function handle() : void
    {
        $card = $this->options['cards']
            ? $this->updateCard($this->cardData)
            : null;

        $this->updateRelationships($this->cardData, $card);

        if ($this->options['prices']) {
            $this->updatePricing($this->cardData, $card);
        }
    }

    private function updateCard(array $cardData) : ?Card
    {
        $set = Set::where('setId', '=', $cardData['set_id'])->first();
        if (!$set) {
            return null;
        }

        $setName = $set->name ?? '';

        echo '    Updating Card: ' . $setName . ': ' . ($cardData['name'] ?? '') . PHP_EOL;

        /** @var Card $card */
        $card = $set->cards()->firstOrCreate([
            'cardId'            => $cardData['id'] ?? null,
        ], [
            'arenaId'             => $cardData['arena_id'] ?? null,
            'languageCode'        => $cardData['lang'] ?? null,
            'mtgoId'              => $cardData['mtgo_id'] ?? null,
            'mtgoFoilId'          => $cardData['mtgo_foil_id'] ?? null,
            'tcgplayerId'         => $cardData['tcgplayer_id'] ?? null,
            'cardmarketId'        => $cardData['cardmarket_id'] ?? null,
            'oracleId'            => $cardData['oracle_id'] ?? null,
            'printsSearchUri'     => $cardData['prints_search_uri'] ?? null,
            'rulingsUri'          => $cardData['rulings_uri'] ?? null,
            'scryfallUri'         => $cardData['scryfall_uri'] ?? null,
            'scryfallApiUri'      => $cardData['uri'] ?? null,
            'artist'              => $cardData['artist'] ?? null,
            'booster'             => $cardData['booster'] ?? null,
            'borderColor'         => $cardData['border_color'] ?? null,
            'cardBackId'          => $cardData['card_back_id'] ?? null,
            'collectorNumber'     => $cardData['collector_number'] ?? null,
            'hasContentWarning'   => $cardData['content_warning'] ?? null,
            'isOnlineOnly'        => $cardData['digital'] ?? null,
            'flavorName'          => $cardData['flavor_name'] ?? null,
            'flavorText'          => $cardData['flavor_text'] ?? null,
            'frameVersion'        => $cardData['frame'] ?? null,
            'isFullArt'           => $cardData['full_art'] ?? null,
            'isHighresImage'      => $cardData['highres_image'] ?? null,
            'illustrationId'      => $cardData['illustration_id'] ?? null,
            'imageStatus'         => $cardData['image_status'] ?? null,
            'printedName'         => $cardData['printed_name'] ?? null,
            'printedText'         => $cardData['printed_text'] ?? null,
            'printedTypeLine'     => $cardData['printed_type_line'] ?? null,
            'isPromo'             => $cardData['promo'] ?? null,
            'rarity'              => $cardData['rarity'] ?? null,
            'releaseDate'         => $cardData['released_at'] ?? null,
            'isReprint'           => $cardData['reprint'] ?? null,
            'scryfallSetId'       => $cardData['set_id'] ?? null,
            'scryfallSetUri'      => $cardData['scryfall_set_uri'] ?? null,
            'isStorySpotlight'    => $cardData['story_spotlight'] ?? null,
            'isTextless'          => $cardData['textless'] ?? null,
            'isVariation'         => $cardData['variation'] ?? null,
            'isVariationOf'       => $cardData['is_variation_of'] ?? null,
            'watermark'           => $cardData['watermark'] ?? null,
            'convertedManaCost'   => $cardData['cmc'] ?? null,
            'edhrecRank'          => $cardData['edhrec_rank'] ?? null,
            'hasFoil'             => $cardData['foil'] ?? null,
            'handModifier'        => $cardData['hand_modifier'] ?? null,
            'lifeModifier'        => $cardData['life_modifier'] ?? null,
            'loyalty'             => $cardData['loyalty'] ?? null,
            'manaCost'            => $cardData['mana_cost'] ?? null,
            'name'                => $cardData['name'] ?? null,
            'name_normalized'     => (new NormalizeString)($cardData['name'] ?? ''),
            'hasNonFoil'          => $cardData['nonfoil'] ?? null,
            'oracleText'          => $cardData['oracle_text'] ?? null,
            'isOversized'         => $cardData['oversized'] ?? null,
            'power'               => $cardData['power'] ?? null,
            'isReserved'          => $cardData['reserved'] ?? null,
            'toughness'           => $cardData['toughness'] ?? null,
            'typeLine'            => $cardData['type_line'] ?? null,
            'layout'              => $cardData['layout'] ?? null,
            'imagePngUri'         => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['png'] : null,
            'imageBorderCropUri'  => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['border_crop'] : null,
            'imageArtCropUri'     => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['art_crop'] : null,
            'imageLargeUri'        => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['large'] : null,
            'imageNormalUri'        => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['normal'] : null,
            'imageSmallUri'         => ($cardData['image_uris'] ?? null)
                ? $cardData['image_uris']['small'] : null,
        ]);

        if (!$card->uuid) {
            $card->update([
                'uuid' => Str::uuid(),
            ]);
        }

        return $card;
    }

    private function updatePricing(array $cardData, ?Card $card = null) : void
    {
        CreateAndPurgePricing::dispatch($cardData, $card);
        // UpdatePricing::dispatch($cardData, $card);
    }

    private function updateRelationships(array $cardData, ?Card $card) : void
    {
        if (!$card) {
            return;
        }

        SetCardSet::dispatch($cardData, $card)->delay(now()->addMinutes(10));
        SetColorFields::dispatch($cardData, $card)->delay(now()->addMinutes(15));
        SetFinishes::dispatch($cardData, $card)->delay(now()->addMinutes(20));
        SetFrameEffects::dispatch($cardData, $card)->delay(now()->addMinutes(25));
        SetKeywords::dispatch($cardData, $card)->delay(now()->addMinutes(30));
        SetGames::dispatch($cardData, $card)->delay(now()->addMinutes(35));
        SetLegalities::dispatch($cardData, $card)->delay(now()->addMinutes(40));
        SetMultiverseIds::dispatch($cardData, $card)->delay(now()->addMinutes(45));
        SetRelatedObjects::dispatch($cardData, $card)->delay(now()->addMinutes(50));
        SetFaces::dispatch($cardData, $card)->delay(now()->addMinutes(55));
        SetPromoTypes::dispatch($cardData, $card)->delay(now()->addMinutes(60));
    }
}
