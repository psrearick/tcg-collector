<?php

namespace App\App\Console\Commands;

use App\Actions\CreateCardObjects as Create;
use Illuminate\Console\Command;

class CreateCardObjects extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create card search data objects';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:cardobjects';

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
        (new Create)();

        return Command::SUCCESS;
    }
}
