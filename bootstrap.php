<?php

namespace Flagrow\Guardian;

use Flarum\Extend;
use Flarum\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Assets('admin'))
        ->asset(__DIR__ . '/js/admin/dist/extension.js')
        ->bootstrapper('flagrow/guardian/main'),

//    (new Extend\Assets('forum'))
//        ->asset(__DIR__ . '/js/forum/dist/extension.js')
//        ->bootstrapper('flagrow/guardian/main'),

    (new Extend\Locales(__DIR__.'/resources/locale')),

    function (Application $app) {
        $app->register(Providers\GuardianServiceProvider::class);

        /** @var Dispatcher $events */
        $events = $app->make(Dispatcher::class);

        $events->subscribe(Listeners\FootPrinting::class);
        $events->subscribe(Listeners\ScoreEvent::class);

        $events->subscribe(Listeners\PreventBots::class);
    }
];
