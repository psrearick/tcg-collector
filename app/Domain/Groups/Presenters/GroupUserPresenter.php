<?php

namespace App\Domain\Groups\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\DataObjects\FolderData;
use App\Domain\Folders\Models\Folder;
use App\Domains\Users\DataObjects\UserData;
use App\Models\User;

class GroupUserPresenter implements PresenterInterface
{
    private ?string $parentUuid;

    private int $userId;

    public function __construct(int $userId, ?string $uuid)
    {
        $this->userId       = $userId;
        $this->parentUuid   = $uuid;
    }

    public function present() : array
    {
        $user                       = User::find($this->userId);
        $userData                   = new UserData($user->toArray());
        $collections                = Collection::where('user_id', '=', $user->id);
        $userData->collection_count = $collections->count();
        $collectionData             = $collections->all()->transform(function ($collection) {
            return new CollectionData($collection->toArray());
        });

        $folders                    = Folder::where('user_id', '=', $user->id);
        $userData->folder_count     = $folders->count();
        $folderData                 = $folders->all()->transform(function ($folder) {
            return new FolderData($folder->toArray());
        });

        return [
            'user'          => $userData,
            'collections'   => $collectionData,
            'folders'       => $folderData,
        ];
    }
}
