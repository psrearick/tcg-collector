<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = optional($request->user())->load('settings');
        if ($user) {
            $settings           = $user->settings;
            $user->settingsData = [
                'card_condition'               => false,
                'price_added'                  => false,
                'expanded_default_edit'        => false,
                'expanded_default_show'        => false,
            ];
            if (optional($settings)->first()) {
                $user->settingsData = [
                    'card_condition'        => $settings->first()->tracks_condition,
                    'price_added'           => $settings->first()->tracks_price,
                    'expanded_default_edit' => $settings->first()->expanded_default_edit,
                    'expanded_default_show' => $settings->first()->expanded_default_show,
                ];
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }
}
