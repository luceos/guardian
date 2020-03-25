<?php

namespace FoF\Guardian;

use Flarum\Extend;
use Flarum\Foundation\Application;
use Flarum\Post\Event\CheckingForFlooding;
use Flarum\User\Event\LoggedIn;
use Flarum\User\Event\Registered;
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
        $events->listen(CheckingForFlooding::class, Actions\FloodGateOperator::class);
        $events->listen([
            Registered::class,
            LoggedIn::class
        ], Actions\PreventBots::class);
    }
];
