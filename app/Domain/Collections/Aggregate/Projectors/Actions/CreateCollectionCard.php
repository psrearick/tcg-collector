<?php

namespace App\Domain\Collections\Aggregate\Projectors\Actions;

use App\Domain\Collections\Models\Collection;

class CreateCollectionCard
{
    public function __invoke(array $values) : void
    {
        Collection::uuid($values['collection'])->cards()
            ->attach($values['card'], $values['values']);
    }
}
