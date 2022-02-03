<?php

namespace Tests\Unit\Domain\Prices\Actions;

use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPricesByFinish;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Support\Str;
use Tests\Feature\Domain\CardCollectionTestCase;

/** @see GetLatestPricesByFinish */
class GetLatestPricesByFinishTest extends CardCollectionTestCase
{
    /**
     * @throws UnknownCurrencyException
     */
    public function test__invoke() : void
    {
        $card = Card::with('prices')->take(1)->get()->first();
        assert($card instanceof Card);

        $latest = (new GetLatestPricesByFinish)($card->uuid);

        foreach ($card->prices as $price) {
            $finish = Str::headline((new MatchType)($price->type));
            $value = Money::ofMinor($price->price, 'USD')->formatTo('en_US');
            $this->assertEquals($value, $latest[$finish]);
        }
    }
}
