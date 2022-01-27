<?php

namespace App\App\Console\Commands;

use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Collections\Models\Collection;
use Illuminate\Console\Command;
use App\Jobs\UpdateAncestry;

class UpdateAllSummaries extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update all collection ancestry totals';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaries:update';

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
        UpdateAncestry::dispatch()->onQueue('long-running-queue');

        return Command::SUCCESS;
    }
}
