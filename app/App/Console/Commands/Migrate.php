<?php

namespace App\App\Console\Commands;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use App\Jobs\MigrateCollectionCard;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Migrate extends Command
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
    protected $signature = 'import:migrate {connection}';

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
        $folders            = DB::connection($conn)->table('folders');
        $collections        = DB::connection($conn)->table('collections');
        $card_collections   = DB::connection($conn)->table('card_collections');

        $userIdMap = [];
        $users->get()->each(function ($user) use (&$userIdMap) {
            $newUser = User::firstOrCreate([
                'email' => $user->email,
            ], [
                'name'              => $user->name,
                'email_verified_at' => $user->email_verified_at,
                'password'          => Hash::make('password'),
                'created_at'        => $user->created_at,
                'updated_at'        => $user->updated_at,
            ]);

            $userIdMap[$user->id] = $newUser->id;
        });

        $folderUuidMap = [];
        $foldersMapped = $folders->get()->collect()
            ->each(function ($folder) use ($userIdMap, &$folderUuidMap) {
                $createFolder = new CreateFolder;
                $uuid = $createFolder(
                    [
                        'name'        => $folder->name,
                        'description' => $folder->description,
                        'user_id'     => $userIdMap[$folder->user_id],
                    ]
                );

                $folder->uuid = $uuid;
                $folderUuidMap[$folder->id] = $uuid;

                return $folder;
            });

        $foldersMapped->whereNotNull('parent_id')->each(function ($folder) use ($foldersMapped, $userIdMap) {
            $parent = $foldersMapped->where('id', '=', $folder->parent_id)->first();
            if (!$parent) {
                return;
            }

            $moveFolder = new MoveFolder;
            $moveFolder($folder->uuid, $parent->uuid, $userIdMap[$folder->user_id]);
        });

        $collectionUuidMap = [];
        $collections->get()->each(function ($collection) use ($folderUuidMap, $userIdMap, &$collectionUuidMap) {
            $createCollection = new CreateCollection;
            $uuid = $createCollection([
                'folder_uuid' => $collection->folder_id
                    ? $folderUuidMap[$collection->folder_id]
                    : null,
                'name'          => $collection->name,
                'description'   => $collection->description,
                'is_public'     => $collection->is_public,
                'user_id'       => $userIdMap[$collection->user_id],
            ]);

            $collectionUuidMap[$collection->id] = $uuid;
        });

        $card_collections->where('quantity', '>', 0)->get()
            ->each(function ($card) use ($collectionUuidMap, $conn) {
                $sourceCard = DB::connection($conn)->table('cards')->find($card->card_id);
                $scryfallId = $sourceCard->cardId;
                $localCard  = Card::where('cardId', '=', $scryfallId)->first();
                $ColUuid    = $collectionUuidMap[$card->collection_id];

                MigrateCollectionCard::dispatch($ColUuid, $localCard->uuid, $card->finish, $card->quantity);
            });

        return Command::SUCCESS;
    }
}
