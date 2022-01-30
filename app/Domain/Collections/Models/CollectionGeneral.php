<?php

namespace App\Domain\Collections\Models;

use App\Domain\Base\Collection;

/**
 * App\Domain\Collections\Models\CollectionGeneral
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
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral newQuery()
 * @method static \Illuminate\Database\Query\Builder|CollectionGeneral onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral query()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereFolderUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionGeneral whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|CollectionGeneral withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CollectionGeneral withoutTrashed()
 * @mixin \Eloquent
 */
class CollectionGeneral extends Collection
{
}
