<?php

namespace Flagrow\Guardian\Listeners;

use Flagrow\Guardian\Events\Configuration;
use Flagrow\Guardian\Events\FootPrinting as FootPrintingEvent;
use Flagrow\Guardian\Exceptions\MissingActorException;
use Flagrow\Guardian\Models\Footprint;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;

class FootPrinting
{
    /**
     * @var Dispatcher
     */
    private $events;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Dispatcher $events, Configuration $configuration)
    {
        $this->events = $events;
        $this->configuration = $configuration;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen($this->configuration->pluck('class')->all(), [$this, 'genericPrint']);
    }

    public function genericPrint($event)
    {
        $configuration = $this->configuration->get(get_class($event), []);

        $getUserBy = array_get($configuration, 'user');

        $userId = array_get(json_decode(json_encode($event), true), "$getUserBy.id");

        $actor = $userId ? User::find($userId) : null;

        if (! $actor) {
            throw new MissingActorException;
        }

        if ($actor->can('actWithoutFootprint')) {
            return;
        }

        $attributes = collect();

        $this->events->dispatch(
            new FootPrintingEvent($event, $actor, $attributes)
        );

        Footprint::newForEvent($event, $actor, $attributes->toArray());
    }
}
