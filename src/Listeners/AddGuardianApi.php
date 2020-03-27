<?php

namespace FoF\Guardian\Listeners;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;

class AddGuardianApi
{
    public function handle(Serializing $event)
    {
        if ($event->isSerializer(ForumSerializer::class)) {
            $settings = app('flarum.settings');

            $event->attributes['guardianEncryptionKey'] = (string) $settings->get('guardian.encryption_key');
        }
    }
}