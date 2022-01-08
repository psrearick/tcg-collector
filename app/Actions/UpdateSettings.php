<?php

namespace App\Actions;

use App\Models\User;

class UpdateSettings
{
    public function __invoke(array $request)
    {
        User::find($request['user_id'])->settings()->updateOrCreate([
            'user_id' => $request['user_id'],
        ], [
            'tracks_condition'  => $request['card_condition'] ?? false,
            'tracks_price'      => $request['price_added'] ?? false,
        ]);
    }
}
