<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'actWithoutFootprint' => Group::MODERATOR_ID,
]);
