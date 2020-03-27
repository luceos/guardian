<?php

use Flarum\Database\Migration;

return Migration::addSettings([
    'guardian.encryption_key' => bin2hex(random_bytes('8')),
]);
