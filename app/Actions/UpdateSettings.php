<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UpdateSettings
{
    public function __invoke(array $request)
    {
        $user = User::find($request['user_id']);

        $user->settings()->updateOrCreate([
            'user_id' => $request['user_id'],
        ], [
            'tracks_condition'      => $request['card_condition'] ?? false,
            'tracks_price'          => $request['price_added'] ?? false,
            'expanded_default_show' => $request['expanded_default_show'] ?? false,
            'expanded_default_edit' => $request['expanded_default_edit'] ?? false,
        ]);

        $user->collections->each(function ($collection) {
            DB::table('collection_card_summaries')
                ->where('collection_uuid', '=', $collection->uuid)
                ->orWhere(function (Builder $query) {
                    $query->where('condition', '=', '')
                        ->orWhereNull('condition');
                })
                ->update(['condition' => 'NM']);
            DB::table('card_collections')
                ->where('collection_uuid', '=', $collection->uuid)
                ->orWhere(function (Builder $query) {
                    $query->where('condition', '=', '')
                        ->orWhereNull('condition');
                })
                ->update(['condition' => 'NM']);
        });
    }
}
