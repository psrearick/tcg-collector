<?php

namespace App\Jobs;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;

class UpdateCardManagementSettings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $settings;

    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->user     = User::find($settings['user_id']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings       = $this->settings;
        $userSettings   = $settings['userSettings'];
        $cardCondition  = false;
        $priceAdded     = false;

        if ($userSettings) {
            $cardCondition    = $userSettings['tracks_condition'] ?? false;
            $priceAdded       = $userSettings['tracks_price'] ?? false;
        }

        $newCardCondition       = $settings['card_condition'] ?? $cardCondition;
        $newPriceAdded          = $settings['price_added'] ?? $priceAdded;
        $newStatus              = $newCardCondition || $newPriceAdded;
        $updatedCardCondition   = $newCardCondition != $cardCondition;
        $updatedPriceAdded      = $newPriceAdded != $priceAdded;

        if (!$updatedCardCondition && !$updatedPriceAdded) {
            return;
        }

        if ($newStatus) {
            $this->separateRecords([
                'priceAdded'    => [
                    'new'       => $newPriceAdded,
                    'old'       => $priceAdded,
                    'updated'   => $updatedPriceAdded,
                ],
                'cardCondition' => [
                    'new'       => $newCardCondition,
                    'old'       => $cardCondition,
                    'updated'   => $updatedCardCondition,
                ],
            ]);

            return;
        }

        $this->mergeRecords();
    }

    private function getCollectionPivots() : Collection
    {
        return $this->user
            ->collections()
            ->withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->get()
            ->load('cards')
            ->whereNull('deleted_at')
            ->keyBy('uuid')
            ->map(function ($collection) {
                return $collection->cards
                    ->whereNull('pivot.deleted_at')
                    ->map(fn ($card) => $card->pivot);
            });
    }

    private function mapGroup(Collection $grouped, string $field, string $default)
    {
        return $grouped->map(function ($group) use ($field, $default) {
            return $group->mapToGroups(function ($pivot) use ($field, $default) {
                $pivot[$field] = $pivot[$field] ?: $default;

                return [(string) $pivot[$field] ?: $default => $pivot];
            });
        });
    }

    private function mergeRecords() : void
    {
        $collections = $this->getCollectionPivots();

        $collectionUuids = $collections->keys();

        CollectionCardSummary::whereIn('collection_uuid', $collectionUuids)->delete();

        $collections->each(function ($collection) {
            $grouped = $collection->mapToGroups(function ($pivot) {
                return [$pivot->card_uuid => $pivot->toArray()];
            });
            
            $grouped->each(function ($group) {
                $this->updateGroup($group);
            });
        });
    }

    private function separateRecords(array $changes) : void
    {
        $collections = $this->getCollectionPivots();

        $collectionUuids = $collections->keys();

        CollectionCardSummary::whereIn('collection_uuid', $collectionUuids)->delete();

        $collections->each(function ($collection) use ($changes) {
            $price = $changes['priceAdded']['new'];
            $condition = $changes['cardCondition']['new'];
            $both = $price && $condition;

            $grouped = $collection->mapToGroups(function ($pivot) {
                return [$pivot->card_uuid => $pivot->toArray()];
            });

            if ($price && !$condition) {
                $grouped = $this->mapGroup($grouped, 'price_when_added', '0.00');
            }

            if ($condition && !$price) {
                $grouped = $this->mapGroup($grouped, 'condition', 'NM');
            }

            if ($both) {
                $grouped = $this->mapGroup($grouped, 'price_when_added', '0.00')
                    ->map(function ($group) {
                        return $this->mapGroup($group, 'condition', 'NM');
                    });
            }

            $grouped->each(function ($group) use ($both) {
                $group->each(function ($subGroup) use ($both) {
                    if ($both) {
                        $subGroup->each(function ($subSubGroup) {
                            $this->updateGroup($subSubGroup);
                        });

                        return;
                    }
                    $this->updateGroup($subGroup);
                });
            });
        });
    }

    private function updateGroup(Collection $group) : void
    {
        $group = $group->mapToGroups(function ($card) {
            return [$card['finish'] => $card];
        });

        $group->each(function ($finishGroup) {
            $sorted = $finishGroup->sortBy('date_added')->values();
            $oldest = $sorted->first();
            $newest = $sorted->last();

            $summary = [
                'collection_uuid'       => $newest['collection_uuid'],
                'card_uuid'             => $newest['card_uuid'],
                'price_when_added'      => $oldest['price_when_added'],
                'price_when_updated'    => $newest['price_when_added'],
                'current_price'         => $newest['price_when_added'],
                'condition'             => $newest['condition'],
                'quantity'              => $sorted->sum('quantity'),
                'finish'                => $newest['finish'],
                'date_added'            => $oldest['date_added'],
            ];

            if ($summary['quantity'] < 1) {
                return;
            }

            CollectionCardSummary::create($summary);
        });
    }
}
