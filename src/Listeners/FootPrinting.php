<?php

namespace FoF\Guardian\Listeners;

use FoF\Guardian\Events\Configuration;
use FoF\Guardian\Events\FootPrinting as FootPrintingEvent;
use FoF\Guardian\Exceptions\MissingActorException;
use FoF\Guardian\Models\Footprint;
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
        /** @var array $configuration */
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
