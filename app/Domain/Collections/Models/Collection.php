<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Collection as BaseCollection;
use App\Traits\BelongsToUserScoped;
use Eloquent;
use Illuminate\Database\Query\Builder;

/**
 * App\Domain\Collections\Models\Collection
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $description
 * @property int|null $is_public
 * @property string|null $folder_uuid
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Folders\Models\AllowedDestination[] $allowedDestinations
 * @property-read int|null $allowed_destinations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Collections\Models\CollectionCardSummary[] $cardSummaries
 * @property-read int|null $card_summaries_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Cards\Models\Card[] $cards
 * @property-read int|null $cards_count
 * @property-read \App\Domain\Folders\Models\Folder|null $folder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\Domain\Prices\Models\Summary|null $summary
 * @property-read \App\Models\User|null $user
 * @method static Builder|Collection inCurrentGroup()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newQuery()
 * @method static Builder|Collection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereFolderUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUuid($value)
 * @method static Builder|Collection withTrashed()
 * @method static Builder|Collection withoutTrashed()
 * @mixin Eloquent
 */
class Collection extends BaseCollection
{
    use BelongsToUserScoped;

    public const SCOPE = 'notShared';
}
