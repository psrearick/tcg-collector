<?php

namespace App\App\Console\Commands;

use App\Actions\CreateCardObjects as Create;
use App\App\Scopes\UserScope;
use App\App\Scopes\UserScopeNotShared;
use App\Domain\Collections\Models\Collection;
use App\Domain\Prices\Aggregate\Actions\UpdateCollectionAncestryTotals;
use App\Jobs\UpdateCollectionSummary;
use Illuminate\Console\Command;

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
        $collections = Collection::
            withoutGlobalScopes([UserScope::class, UserScopeNotShared::class])
            ->whereNull('deleted_at')
            ->get();

        $collections->each(function ($collection) {
            UpdateCollectionSummary::dispatch($collection);
        });

        return Command::SUCCESS;
    }
}
