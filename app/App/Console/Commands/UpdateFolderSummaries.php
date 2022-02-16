<?php

namespace App\App\Console\Commands;

use App\Jobs\UpdateFolderAncestry;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as Response;

class UpdateFolderSummaries extends Command
{
    protected $description = 'Update folder summaries';

    protected $signature = 'summaries:updateFolders';

    public function handle() : int
    {
        UpdateFolderAncestry::dispatch();

        return Response::SUCCESS;
    }
}

