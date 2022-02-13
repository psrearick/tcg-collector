<?php

namespace App\App\Console\Commands;

use App\Jobs\ImportScryfallData;
use Symfony\Component\Console\Command\Command;

class ImportPricing extends Command
{
    protected string $description = 'Import pricing data from scryfall';

    protected string $signature = 'import:prices';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle() : int
    {
        $options = [
            'prices'    => true,
            'symbols'   => false,
            'cards'     => false,
            'sets'      => false,
        ];

        ImportScryfallData::dispatch($options)->onQueue('long-running-queue');

        return Command::SUCCESS;
    }
}
