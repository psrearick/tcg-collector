<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_owners_cant_leave_their_own_team()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->delete('/teams/' . $user->currentTeam->id . '/members/' . $user->id);

        $response->assertSessionHasErrorsIn('removeTeamMember', ['team']);

        $this->assertNotNull($user->currentTeam->fresh());
    }

    public function test_users_can_leave_teams()
    {
        $user = User::factory()->withPersonalTeam()->create();

        /** 
         * @var \App\Models\User
         */
        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->delete('/teams/' . $user->currentTeam->id . '/members/' . $otherUser->id);

        $this->assertCount(0, $user->currentTeam->fresh()->users);
    }
}
