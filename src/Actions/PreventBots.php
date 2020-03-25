<?php

namespace FoF\Guardian\Actions;

use Carbon\Carbon;
use Flarum\Extension\ExtensionManager;
use Flarum\User\Event\LoggedIn;
use Flarum\User\Event\Registered;
use Jenssegers\Agent\Agent;

class PreventBots
{
    /**
     * @var Agent
     */
    private $agent;
    /**
     * @var ExtensionManager
     */
    private $extensions;

    public function __construct(Agent $agent, ExtensionManager $extensions)
    {
        $this->agent = $agent;
        $this->extensions = $extensions;
    }

    /**
     * @param Registered|LoggedIn $event
     */
    public function handle($event)
    {
        if (!$this->agent->isRobot()) {
            return;
        }

        if ($this->extensions->getEnabledExtensions()->has('flarum-suspend')) {
            $event->user->suspend_until = (new Carbon())->addCentury();
        } else {
            $event->user->is_email_confirmed = false;
        }
    }
}
