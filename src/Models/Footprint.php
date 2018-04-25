<?php

namespace Flagrow\Guardian\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property string $ip
 * @property string $hostname
 * @property string $accept_language
 * @property string $user_agent
 * @property bool $do_not_track
 * @property string $timezone
 * @property string $operating_system
 * @property string $email
 * @property string $locale
 * @property string $event
 * @property int $score
 * @property int $since_last_event
 * @property Carbon $created_at
 */
class Footprint extends AbstractModel
{
    protected $table = 'guardian_footprint';

    protected $casts = [
        'do_not_track' => 'boolean'
    ];

    protected $dates = ['created_at'];

    public static function newForEvent($event, array $attributes): Footprint
    {
        /** @var ServerRequestInterface $request */
        $request = Request::createFromGlobals();

        $footprint = new Self;

        $footprint->ip = $request->getHeader('remote-addr');
        $footprint->hostname = !$footprint->ip ?: gethostbyaddr($footprint->ip);

        $footprint->accept_language = $request->getHeader('accept-language');
        $footprint->user_agent = $request->getHeader('user-agent');
        $footprint->do_not_track = $request->getHeader('dnt');

        return $footprint;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
