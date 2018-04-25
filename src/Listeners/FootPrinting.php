<?php

namespace Flagrow\Guardian\Listeners;

use Flarum\User\Event as UserEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Zend\Diactoros\Request;

class FootPrinting
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen([UserEvent\LoggedIn::class, UserEvent\Registered::class], [$this, 'authenticated']);
        $events->listen(UserEvent\Saving::class, [$this, 'suspended']);
        $events->listen(UserEvent\EmailChanged::class, [$this, 'emailChanged']);
    }

    /**
     * @param UserEvent\LoggedIn|UserEvent\Registered $event
     */
    public function authenticated($event)
    {

    }

    public function suspended(UserEvent\Saving $event)
    {

    }

    public function emailChanged(UserEvent\EmailChanged $event)
    {

    }
}
