<?php

namespace FoF\Guardian\Exceptions;


use Flarum\Foundation\ErrorHandling\HandledError;

class BotExceptionHandler
{
    public function handle(BotException $e)
    {
        return new HandledError($e, 'guardian_exception', 403);
    }
}