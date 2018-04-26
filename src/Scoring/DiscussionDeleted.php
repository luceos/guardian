<?php

namespace Flagrow\Guardian\Scoring;

use Flagrow\Guardian\Contracts\Hook;
use Flagrow\Guardian\Events\FootPrinting;
use Flarum\Discussion\Event\Deleted;

class DiscussionDeleted implements Hook
{
    public function __invoke(FootPrinting $event)
    {
        /** @var Deleted  $event */
        $event = $event->event;

        if ($event->discussion->comments_count > 1) {
            return -5;
        }

        return -10;
    }
}
