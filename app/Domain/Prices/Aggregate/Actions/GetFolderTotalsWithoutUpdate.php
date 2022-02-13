<?php

namespace App\Domain\Prices\Aggregate\Actions;

use App\Domain\Folders\Models\Folder;

class GetFolderTotalsWithoutUpdate
{
    private GetFolderTotals $getFolderTotals;

    public function __construct()
    {
        $this->getFolderTotals = new GetFolderTotals();
    }

    public function __invoke(Folder $folder) : array
    {
        return ($this->getFolderTotals)($folder);
    }
}
