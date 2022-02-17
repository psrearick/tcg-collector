<?php

namespace App\Console\Commands;

use App\Jobs\UpdateCollectionAncestry;
use App\Jobs\UpdateFolderAncestry;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as Response;

class UpdateSummaries extends Command
{
    /** @var string */
    protected $description = 'update all ancestry totals';

    /** @var string */
    protected $signature = 'summaries:update {--collections} {--folders}';

    public function handle() : int
    {
        $folders = $this->option('folders')
            ? 'folders'
            : null;

        $type = $this->option('collections')
            ? 'collections'
            : $folders;

        if (!$type) {
            $type = $this->choice('Which you like to update?', ['collections', 'folders'], 0, 3);
        }

        if ($type === 'collections') {
            UpdateCollectionAncestry::dispatch();

            return Response::SUCCESS;
        }

        UpdateFolderAncestry::dispatch();

        return Response::SUCCESS;
    }
}
