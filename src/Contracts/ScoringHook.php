<?php

namespace Flagrow\Guardian\Contracts;

use Flagrow\Guardian\Events\FootPrinting;

interface ScoringHook
{
    public function __invoke(FootPrinting $event);
}
