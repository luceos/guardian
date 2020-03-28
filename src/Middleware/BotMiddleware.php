<?php

namespace FoF\Guardian\Middleware;

use DateTime;
use FoF\Guardian\Exceptions\BotException;
use Illuminate\Support\Arr;
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
        $time = $this->getEncryptedTime('0d5256759324091451b6baeaedfb7bd7');
        

        if (!$honeyPotName) {
            return $handler->handle($request);
        }

        if (!empty($data[$honeyPotName]) || $time > new DateTime()) {
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
        $date = new DateTime();

        return $date->setTimestamp(substr(
                \openssl_decrypt(
                    hex2bin($time),
                    'aes-128-cbc',
                    app('flarum.settings')->get('guardian.encryption_key'),
                    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
                ), 0, -6)
        );
    }

}