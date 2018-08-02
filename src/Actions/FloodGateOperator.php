<?php

namespace Flagrow\Guardian\Actions;

use Flagrow\Guardian\Models\Footprint;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Post\Event\CheckingForFlooding;

class FloodGateOperator
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ChecksForFlooding::class, [$this, 'operate']);
    }

    public function operate(CheckingForFlooding $event)
    {
        if ($event->actor->can('actWithoutFlooding')) {
            return false;
        }

        $score = Footprint::totalScoreForUser($event->actor);

        // @todo
    }
}
