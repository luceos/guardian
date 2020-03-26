<?php

namespace FoF\Guardian\Markers;

use DateTime;
use FoF\Guardian\Contracts\Marker;
use FoF\Guardian\Events\FootPrinting;

class UserSuspended implements Marker
{
    public function __invoke(FootPrinting $event)
    {
        /** @var Suspended $event */
        $event = $event->event;

        // We subtract 10 seconds to the suspension due to small time differences that cause
        // the function to return as 1 less day.
        $daysSuspended = (new DateTime('-10 seconds'))->diff($event->user->suspended_until)->days;

        // Log function that makes bans immediately very costly to karma but essentially cap on total karma
        // loss after a few days
        $points = intval(round(-65 * (log($daysSuspended/2, 12) + 1)));

        return $points;
    }
}