<?php

namespace Flagrow\Guardian\Listeners;

use Flagrow\Guardian\Models\Footprint;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Post\Event\ChecksForFlooding;

class FloodGateOperator
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ChecksForFlooding::class, [$this, 'operate']);
    }

    public function operate(ChecksForFlooding $event)
    {
        if ($event->actor->can('actWithoutFlooding')) {
            return false;
        }

        $score = Footprint::totalScoreForUser($event->actor);

        // @todo
    }
}
