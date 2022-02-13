<?php

namespace App\App\Console\Commands;

use App\Jobs\ImportScryfallData;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ImportPricing extends Command
{
    protected $description = 'Import pricing data from scryfall';

    protected $signature = 'import:prices';

    public function handle() : int
    {
        $options = [
            'prices'    => true,
            'symbols'   => false,
            'cards'     => false,
            'sets'      => false,
        ];

        ImportScryfallData::dispatch($options)->onQueue('long-running-queue');

        return SymfonyCommand::SUCCESS;
    }
}
