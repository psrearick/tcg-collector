<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Jobs\UpdateCardManagementSettings;
use App\Models\User;

class RecalculateCollectionCardSummaries
{
    public function __invoke(array $settings) : void
    {
        $user                       = User::find($settings['user_id']);
        $this->user                 = $user;
        $userSettings               = optional(optional($user->settings)->first())->toArray();
        $settings['userSettings']   = $userSettings;
        UpdateCardManagementSettings::dispatch($settings);
    }
}
