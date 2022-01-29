<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\UpdateCardManagementSettings;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\Assert;
use Tests\Feature\Domain\CardCollectionTestCase;

/**
 * @see \App\Http\Controllers\SettingsController
 */
class SettingsControllerTest extends CardCollectionTestCase
{
    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        $response = $this->get(route('settings.show'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component('Settings/Show'));
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        Bus::fake();

        $user = $this->act();

        $response = $this->patch(route('settings.update-settings'), [
            'user_id'          => $user->id,
            'tracks_price'     => true,
            'tracks_condition' => true,
        ]);

        $response->assertRedirect();

        Bus::assertDispatched(UpdateCardManagementSettings::class);
    }
}
