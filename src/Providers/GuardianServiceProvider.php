<?php

namespace FoF\Guardian\Providers;

use Illuminate\Encryption\Encrypter;
use Flarum\Foundation\AbstractServiceProvider;
use FoF\Guardian\Events\Configuration;
use Symfony\Component\Yaml\Yaml;

class GuardianServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->app->singleton(Configuration::class, function () {
            foreach ([
                         storage_path('guardian/guardian.yaml'),
                         base_path('guardian.yaml'),
                         __DIR__ . '/../../resources/configs/guardian.yaml'
                     ] as $path) {
                if (file_exists($path)) {
                    break;
                }
            }

            $config = (new Configuration(file_exists($path) ? Yaml::parseFile($path) : []))
            ->map(function ($subject, $key) {
                if (is_array($subject)) {
                    return collect($subject)->keyBy($key === 'events' ? 'class' : 'name');
                }

                return $subject;
            });;

            return $config;
        });

        $this->app->singleton('guardian.encrypter', function () {
            return new Encrypter((string) $this->app('flarum.settings')->get('guardian.encryption_key'), 'AES-256-CBC');
        });
    }
}
