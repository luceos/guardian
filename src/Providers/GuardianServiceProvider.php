<?php

namespace FoF\Guardian\Providers;

use FoF\Guardian\Events\Configuration;
use Flarum\Foundation\AbstractServiceProvider;
use Symfony\Component\Yaml\Yaml;

class GuardianServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->app->singleton(Configuration::class, function () {
            foreach ([
                         storage_path('guardian/events.yaml'),
                         base_path('guardian.yaml'),
                         __DIR__ . '/../../resources/configs/events.yaml'
                     ] as $path) {
                if (file_exists($path)) {
                    break;
                }
            }

            return (new Configuration(file_exists($path) ? Yaml::parseFile($path) : []))
                ->keyBy('class');
        });
    }
}
