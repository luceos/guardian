<?php

namespace Flagrow\Guardian\Markers;

use Flagrow\Guardian\Contracts\Marker;
use Flagrow\Guardian\Events\FootPrinting;
use Flarum\Discussion\Event\Deleted;

class DiscussionDeleted implements Marker
{
    public function __invoke(FootPrinting $event)
    {
        /** @var Deleted  $event */
        $event = $event->event;

        if ($event->discussion->comment_count > 1) {
            return -5;
        }

        return -10;
    }
}
