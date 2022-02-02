<?php

namespace App\App\Contracts;

interface PresentsPrintings
{
    public function getPrintingsPresent() : array;

    public function setPrintings() : void;
}
