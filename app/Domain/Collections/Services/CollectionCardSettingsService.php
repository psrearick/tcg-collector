<?php

namespace App\Domain\Collections\Services;

use App\Models\User;

class CollectionCardSettingsService
{
    public static function getSettings(?User $user = null) : array
    {
        $settingsData = [
            'card_condition'    => null,
            'price_added'       => null,
        ];
        $user = $user ?: optional(auth()->user())->load('settings');
        if ($user) {
            $settings  = $user->settings;
            if (optional($settings)->first()) {
                $settingsData = [
                    'card_condition'    => $settings->first()->tracks_condition,
                    'price_added'       => $settings->first()->tracks_price,
                ];
            }
        }
        
        return $settingsData;
    }

    public static function tracksCondition(?User $user = null) : bool
    {
        $settings = self::getSettings($user);
        return $settings['card_condition'] ?: false;
    }

    public static function tracksPrice(?User $user = null) : bool
    {
        $settings = self::getSettings($user);
        return $settings['price_added'] ?: false;
    }
}
