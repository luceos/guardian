<?php

namespace FoF\Guardian\Middleware;

use Illuminate\Support\Arr;
use FoF\Guardian\Exceptions\BotException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BotMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->getMethod() === 'POST') {
            return $handler->handle($request);
        }

        $data = Arr::get($request->getParsedBody(), 'data', []);

        $honeyPotName = $this->getHoneyPotName($data);

        if (!$honeyPotName) {
            return $handler->handle($request);
        }

        if (!empty($data[$honeyPotName])) {
            throw new BotException;
        }

        $time = $this->getEncryptedTime($data['timestamp']);

        if ($time > new DateTime()) {
            throw new BotException;
        }

        return $handler->handle($request);
    }

    private function getHoneyPotName($requestFields)
    {
        return collect($requestFields)->filter(function ($value, $key) {
            return Str::startsWith($key, 'guardian');
        })->keys()->first();
    }

    private function getEncryptedTime($time)
    {
        new DateTime(app('guardian.encrypter')->decrypt($time));
    }
}