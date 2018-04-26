<?php

namespace Flagrow\Guardian\Contracts;

use Flagrow\Guardian\Events\FootPrinting;

interface Hook
{
    public function __invoke(FootPrinting $event);
}
