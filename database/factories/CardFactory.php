<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Actions\NormalizeString;
use App\Domain\Cards\Models\Card;

class CardFactory extends Factory
{
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->words(3, true);

        $borderColors = [
            'black',
            'white',
            'borderless',
            'silver',
            'gold',
        ];

        $layouts = [
            'normal',
            'saga',
            'adventure'
        ];

        $frames = [
            '1993',
            '1997',
            '2003',
            '2015',
            'future'
        ];

        $rarities = [
            'common',
            'uncommon',
            'rare',
            'special',
            'mythic',
            'bonus'
        ];

        return [
            'cardId'              => Str::uuid()->toString(),
            'name'                => $name,
            'name_normalized'     => (new NormalizeString)($name),
            'collectorNumber'     => $this->faker->text(5),
            'borderColor'         => $borderColors[$this->faker->numberBetween(0, 4)],
            'languageCode'        => 'en',
            'layout'              => $layouts[$this->faker->numberBetween(0, 2)],
            'isFullArt'           => $this->faker->boolean(5),
            'isPromo'             => $this->faker->boolean(1),
            'isTextless'          => $this->faker->boolean(1),
            'frameVersion'        => $frames[$this->faker->numberBetween(0, 4)],
            'isOnlineOnly'        => false,
            'oracleId'            => Str::uuid()->toString(),
            'rarity'              => $rarities[$this->faker->numberBetween(0, 5)],
            'releaseDate'         => $this->faker->numberBetween(1993, 2022),
            'convertedManaCost'   => $this->faker->numberBetween(0, 10),
            'imageNormalUri'      => $this->faker->url() . Str::uuid()->toString(),

            // 'manaCost'            => $cardData['mana_cost'] ?? null,
            // 'printsSearchUri'     => $cardData['prints_search_uri'] ?? null,
            // 'rulingsUri'          => $cardData['rulings_uri'] ?? null,
            // 'scryfallUri'         => $cardData['scryfall_uri'] ?? null,
            // 'scryfallApiUri'      => $cardData['uri'] ?? null,
            // 'artist'              => $cardData['artist'] ?? null,
            // 'booster'             => $cardData['booster'] ?? null,
            // 'cardBackId'          => $cardData['card_back_id'] ?? null,
            // 'hasContentWarning'   => $cardData['content_warning'] ?? null,
            // 'flavorName'          => $cardData['flavor_name'] ?? null,
            // 'flavorText'          => $cardData['flavor_text'] ?? null,
            // 'isHighresImage'      => $cardData['highres_image'] ?? null,
            // 'illustrationId'      => $cardData['illustration_id'] ?? null,
            // 'imageStatus'         => $cardData['image_status'] ?? null,
            // 'printedName'         => $cardData['printed_name'] ?? null,
            // 'printedText'         => $cardData['printed_text'] ?? null,
            // 'printedTypeLine'     => $cardData['printed_type_line'] ?? null,
            // 'isReprint'           => $cardData['reprint'] ?? null,
            // 'scryfallSetId'       => $cardData['set_id'] ?? null,
            // 'scryfallSetUri'      => $cardData['scryfall_set_uri'] ?? null,
            // 'isStorySpotlight'    => $cardData['story_spotlight'] ?? null,
            // 'isVariation'         => $cardData['variation'] ?? null,
            // 'isVariationOf'       => $cardData['is_variation_of'] ?? null,
            // 'watermark'           => $cardData['watermark'] ?? null,
            // 'edhrecRank'          => $cardData['edhrec_rank'] ?? null,
            // 'handModifier'        => $cardData['hand_modifier'] ?? null,
            // 'lifeModifier'        => $cardData['life_modifier'] ?? null,
            // 'loyalty'             => $cardData['loyalty'] ?? null,
            // 'oracleText'          => $cardData['oracle_text'] ?? null,
            // 'isOversized'         => $cardData['oversized'] ?? null,
            // 'power'               => $cardData['power'] ?? null,
            // 'isReserved'          => $cardData['reserved'] ?? null,
            // 'toughness'           => $cardData['toughness'] ?? null,
            // 'typeLine'            => $cardData['type_line'] ?? null,
            // 'imagePngUri'         => ($cardData['image_uris'] ?? null)
            //     ? $cardData['image_uris']['png'] : null,
            // 'imageBorderCropUri'  => ($cardData['image_uris'] ?? null)
            //     ? $cardData['image_uris']['border_crop'] : null,
            // 'imageArtCropUri'     => ($cardData['image_uris'] ?? null)
            //     ? $cardData['image_uris']['art_crop'] : null,
            // 'imageLargeUri'        => ($cardData['image_uris'] ?? null)
            //     ? $cardData['image_uris']['large'] : null,
            // 'imageSmallUri'         => ($cardData['image_uris'] ?? null)
            //     ? $cardData['image_uris']['small'] : null,
        ];
    }
}
