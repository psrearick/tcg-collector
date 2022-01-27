<?php

namespace App\App\Console\Commands;

use App\Jobs\UpdateAncestry;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as Response;

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

    public function handle() : int
    {
        UpdateAncestry::dispatch()->onQueue('long-running-queue');

        return Response::SUCCESS;
    }
}
