<?php

namespace Tests\Feature\Domain;

use App\Models\User;
use Database\Seeders\CardsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Domain\Traits\WithCollectionCards;
use Tests\TestCase;

abstract class CardCollectionTestCase extends TestCase
{
    use RefreshDatabase, WithCollectionCards, WithFaker;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(CardsSeeder::class);
        $this->act();
    }

    protected function act(?User $user = null) : User
    {
        if (!$user) {
            $user = $this->getNewUser();
        }

        $this->actingAs($user);

        return $user;
    }

    protected function getNewUser() : User
    {
        return User::factory()->withPersonalTeam()->create();
    }
}
