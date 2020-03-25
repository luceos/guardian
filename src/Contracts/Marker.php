<?php

namespace FoF\Guardian\Contracts;

use FoF\Guardian\Events\FootPrinting;

interface Marker
{
    public function __invoke(FootPrinting $event);
}
