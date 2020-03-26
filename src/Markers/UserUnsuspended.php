<?php

namespace FoF\Guardian\Markers;

use DateTime;
use FoF\Guardian\Contracts\Marker;
use FoF\Guardian\Events\FootPrinting;
use FoF\Guardian\Models\Footprint;

class UserUnsuspended implements Marker
{
    public function __invoke(FootPrinting $event)
    {
        /** @var Suspended $event */
        $event = $event->event;

        $suspendEntry = Footprint::where([
            ['event', 'Flarum\Suspend\Event\Suspended'],
            ['user_id', $event->user->id]
        ])->get()->last();

        // If a user is unsuspended before their scheduled time, they should some of their karma back.
        $daysSince = abs((new DateTime())->diff($suspendEntry->created_at)->days);

        $karmaLost = $suspendEntry->score;

        // Calculate the amount of Karma the user should get back
        $points = abs($karmaLost - intval(round(-65 * (log(($daysSince)/2, 12) + 1))));

        return $points;
    }
}