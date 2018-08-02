<?php

namespace Flagrow\Guardian\Contracts;

use Flagrow\Guardian\Events\FootPrinting;

interface Marker
{
    public function __invoke(FootPrinting $event);
}
