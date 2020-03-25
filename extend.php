<?php

namespace FoF\Guardian;

use Flarum\Extend;
use Flarum\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

//    (new Extend\Frontend('forum'))
//        ->js(__DIR__ . '/js/forum/dist/extension.js')

    new Extend\Locales(__DIR__.'/resources/locale'),

    function (Application $app) {
        $app->register(Providers\GuardianServiceProvider::class);

        /** @var Dispatcher $events */
        $events = $app->make(Dispatcher::class);

        $events->subscribe(Listeners\FootPrinting::class);
        $events->subscribe(Listeners\ScoreEvent::class);

        // Actions.
        $events->subscribe(Actions\PreventBots::class);
        $events->subscribe(Actions\FloodGateOperator::class);
    }
];
