<?php

namespace Flagrow\Guardian\Models;

use Flarum\Database\AbstractModel;

class Footprint extends AbstractModel
{
    protected $table = 'guardian_footprint';

    public static function newForEvent($event, array $attributes)
    {
        
    }
}
