<?php

namespace App\Domain\Folders\Models;

use App\Domain\Folders\Models\FolderRoot;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends FolderRoot
{
    use NodeTrait;
}
