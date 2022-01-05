<?php

namespace App\App\Console\Commands;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Models\Collection;
use App\Jobs\MigrateCollectionCard;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrateUpdate extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate collections from a different database connection';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:migrateUpdate {connection}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conn               = $this->argument('connection');
        $users              = DB::connection($conn)->table('users');
        $collections        = DB::connection($conn)->table('collections');
        $card_collections   = DB::connection($conn)->table('card_collections');

        $userIdMap = [];
        $users->get()->each(function ($user) use (&$userIdMap) {
            $newUser = $this->createUser($user);

            $this->createTeam($newUser);

            $userIdMap[$user->id] = $newUser->id;
        });

        return;

        $collectionUuidMap = [];
        $collections->get()->each(function ($collection) use ($userIdMap, &$collectionUuidMap) {
            $record = Collection::where('name', '=', $collection->name)
                ->where('description', 'like', DB::raw('"' . $collection->description . '"'))
                ->where('is_public', '=', $collection->is_public)
                ->where('user_id', '=', $userIdMap[$collection->user_id])
                ->first();

            $collectionUuidMap[$collection->id] = $record->uuid;
        });

        $card_collections->where('quantity', '>', 0)->get()
            ->each(function ($card) use ($collectionUuidMap, $conn) {
                $sourceCard = DB::connection($conn)->table('cards')->find($card->card_id);
                $scryfallId = $sourceCard->cardId;
                $localCard  = Card::where('cardId', '=', $scryfallId)->first();
                $colUuid    = $collectionUuidMap[$card->collection_id];

                $colCard = Collection::uuid($colUuid)->cards()
                    ->where('uuid', '=', $localCard->uuid)
                    ->wherePivot('finish', '=', $card->finish)
                    ->first();

                if ($colCard) {
                    return;
                }

                print $localCard->name;

                MigrateCollectionCard::dispatch($colUuid, $localCard->uuid, $card->finish, $card->quantity);
            });

        return Command::SUCCESS;
    }

    protected function createTeam(User $user) : void
    {
        $teamData = [
            'user_id'       => $user->id,
            'name'          => explode(' ', $user->name, 2)[0] . "'s Group",
            'personal_team' => true,
        ];

        $team = Team::where('user_id', '=', $teamData['user_id'])->where('name', '=', $teamData['name'])->first();
        if ($team) {
            return;
        }

        $user->ownedTeams()->save(Team::forceCreate($teamData));
    }

    protected function createUser(object $user) : User
    {
        return User::firstOrCreate([
            'email' => $user->email,
        ], [
            'name'              => $user->name,
            'email_verified_at' => $user->email_verified_at,
            'password'          => Hash::make('password'),
            'created_at'        => $user->created_at,
            'updated_at'        => $user->updated_at,
        ]);
    }
}
