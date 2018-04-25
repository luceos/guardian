<?php

namespace Flagrow\Guardian;

use Flarum\Extend;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Assets('admin'))
        ->asset(__DIR__ . '/js/admin/dist/extension.js')
        ->bootstrapper('flagrow/guardian/main'),

    (new Extend\Assets('forum'))
        ->asset(__DIR__ . '/js/forum/dist/extension.js')
        ->bootstrapper('flagrow/guardian/main'),

    function (Dispatcher $events) {
        $events->subscribe(Listeners\FootPrinting::class);
    }
];
