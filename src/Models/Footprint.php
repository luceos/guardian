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

        $footprint->ip = $request->getClientIp();
        $footprint->hostname = !$footprint->ip ?: gethostbyaddr($footprint->ip);
        $footprint->user_agent = $request->headers->get('user-agent');
        $footprint->do_not_track = $request->headers->get('dnt');

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
     * @return Footprint|null
     */
    public static function lastByUser(User $user)
    {
        return Footprint::query()->where('user_id', $user->id)->latest('created_at')->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function totalScoreForUser(User $user): int
    {
        $footprints = Footprint::query()->where('user_id', $user->id)->get();

        $totalVotes = $footprints->sum('score');
        $positiveVotes = $footprints->where('is_negative', false)->sum('score');

        die(var_dump(tatic::getLowerBound($positiveVotes, $totalVotes)));

        return (float) $totalVotes ? static::getLowerBound($positiveVotes, $totalVotes) : 0;
    }

    /**
     * Run a Wilson Confidence Interval on the data to determine
     * the lower bound of positive karma. More data (and positive karma) will equal a higher lower bound
     * while less data is uncertain and will have a lower lower bound.
     *
     * @param int $positiveVotes
     * @param int $totalVotes
     * @return float|int
     */
    private static function getLowerBound(int $positiveVotes, int $totalVotes)
    {
        $confidence = 1.959964;
        $prop = 1.0 * $positiveVotes / $totalVotes;
        $numerator   = $prop + $confidence * $confidence / (2 * $totalVotes) - $confidence * sqrt(($prop * (1 - $prop) + $confidence * $confidence / (4 * $totalVotes)) / $totalVotes);
        $denominator = 1 + $confidence * $confidence / $totalVotes;

        return $numerator / $denominator;
    }

    public static function averageBetweenTimeForUser(User $user): int
    {
        return Footprint::query()->where('user_id', $user->id)->average('since_last_event') ?? 0;
    }
}
