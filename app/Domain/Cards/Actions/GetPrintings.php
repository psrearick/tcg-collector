<?php

namespace App\Domain\Cards\Actions;

use App\Domain\Cards\Models\Card;
use Illuminate\Database\Eloquent\Collection;

class GetPrintings
{
    public function __invoke(string $oracleId) : Collection
    {
        return Card::where('oracleId', '=', $oracleId)->get();
    }
}
