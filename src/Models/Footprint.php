<?php

namespace FoF\Guardian\Models;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;
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
 * @property string $device
 * @property string $browser
 * @property string $robot
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

    public static function newForEvent($event, User $actor, array $attributes = []): Footprint
    {
        /** @var Request $request */
        $request = Request::createFromGlobals();

        /** @var Footprint $footprint */
        $footprint = Footprint::unguarded(function () use ($attributes) {
            return new Footprint($attributes);
        });

        $footprint->event = get_class($event);

        // Get optional Cloudflare headers
        $cf = $request->headers->get('CF-Connecting-IP');
        $country = $request->headers->get('cf-ipcountry');

        $footprint->ip = $cf ? $cf : $request->getClientIp();
        $footprint->hostname = !$footprint->ip ?: gethostbyaddr($footprint->ip);
        $footprint->user_agent = $request->headers->get('user-agent');
        $footprint->country = $country;
        $footprint->do_not_track = (bool) $request->headers->get('dnt');

        $footprint->is_negative = $attributes['score'] < 0 ? true : false;

        /** @var Agent $agent */
        $agent = app()->make(Agent::class);

        $footprint->accept_language = implode(',', $agent->languages());
        $footprint->operating_system = $agent->platform();
        $footprint->device = $agent->device();
        $footprint->browser = $agent->browser();

        if ($agent->isRobot()) {
            $footprint->robot = $agent->robot();
        }

        $footprint->user()->associate($actor);

        $footprint->email = $actor->email;
        $footprint->locale = $actor->locale;

        $last = optional(static::lastByUser($actor))->created_at ?? $actor->joined_at;

        $footprint->since_last_event = $last->diffInSeconds();

        $footprint->save();

        return $footprint;
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function lastByUser(User $user)
    {
        return Footprint::query()->where('user_id', $user->id)->latest('created_at')->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function totalScoreForUser(User $user): float
    {
        $footprints = Footprint::query()->where('user_id', $user->id)->get();

        $positiveVotes = $footprints->where('is_negative', false)->sum('score');

        $totalVotes = $positiveVotes + abs($footprints->where('is_negative', true)->sum('score'));

        return (float) $totalVotes ? static::getLowerBound($positiveVotes, $totalVotes) : 0;
    }

    public static function averageBetweenTimeForUser(User $user): int
    {
        return Footprint::query()->where('user_id', $user->id)->average('since_last_event') ?? 0;
    }

    /**
     * Run a Wilson Confidence Interval on the data to determine
     * the lower bound of positive karma. More data (and positive karma) will equal a higher lower bound
     * while less data is uncertain and will have a lower lower bound.
     *
     * @param int $positiveVotes
     * @param int $totalVotes
     * @return float
     */
    private static function getLowerBound(int $positiveVotes, int $totalVotes) : float
    {
        $confidence = 1.959964;
        $prop = 1.0 * $positiveVotes / $totalVotes;
        $numerator   = $prop + $confidence * $confidence / (2 * $totalVotes) - $confidence * sqrt(($prop * (1 - $prop) + $confidence * $confidence / (4 * $totalVotes)) / $totalVotes);
        $denominator = 1 + $confidence * $confidence / $totalVotes;

        return $numerator / $denominator;
    }
}
