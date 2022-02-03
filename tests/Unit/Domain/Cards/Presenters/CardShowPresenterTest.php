<?php

namespace Tests\Unit\Domain\Cards\Presenters;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Cards\Presenters\CardShowPresenter;
use Tests\Feature\Domain\CardCollectionTestCase;

/** @see CardShowPresenter */
class CardShowPresenterTest extends CardCollectionTestCase
{
    public function test_card_presenter_can_return_card_details() : void
    {
        $collectionUuid = $this->createCollection();
        $cardUuid       = $this->createCollectionCard($collectionUuid);
        $card           = Card::uuid($cardUuid);
        $presentation   = (new CardShowPresenter($cardUuid))->present();

        $build          = (new BuildCard($card))
            ->add('feature')
            ->add('image_url')
            ->add('set_image_url')
            ->get();

        $fields         = [
            'name'              => 'name',
            'uuid'              => 'uuid',
            'features'          => 'feature',
            'image'             => 'image_url',
            'set_image'         => 'set_image_url',
            'collector_number'  => 'collectorNumber',
            'type'              => 'typeLine',
            'mana_cost'         => 'manaCost',
            'oracle_text'       => 'oracleText',
            'artist'            => 'artist',
        ];

        foreach ($fields as $field => $value) {
            $this->assertEquals($build[$value], $presentation[$field]);
        }
    }

//    public function test_card_presenter_returns_accurate_prices() : void
//    {
//        $collectionUuid = $this->createCollection();
//        $cardUuid       = $this->createCollectionCard($collectionUuid);
//        $card           = Card::uuid($cardUuid);
//        $presentation   = (new CardShowPresenter($cardUuid))->present();
//    }
}
