<?php

namespace Flagrow\Guardian\Events;

use Flarum\User\User;
use Illuminate\Support\Collection;

class FootPrinting
{
    public $event;
    /**
     * @var User
     */
    public $actor;
    /**
     * @var Collection
     */
    public $attributes;

    public function __construct($event, User $actor, Collection &$attributes)
    {
        $this->event = $event;
        $this->actor = $actor;
        $this->attributes = &$attributes;
    }
}
