<?php

namespace App\Domain\Stores\Actions;

use App\Domain\Stores\DataObjects\StoreData;
use App\Domain\Stores\Models\Store;

class CreateStore
{
    public function __invoke(array $request)
    {
        $data = new StoreData($request);
        Store::create($data->toArray());
    }
}
