<?php

namespace FoF\Guardian\Actions;

use DateTime;
use FoF\Guardian\Models\Footprint;
use FoF\Guardian\Events\Configuration;
use Flarum\Post\Event\CheckingForFlooding;
use Flarum\Post\Post;

class FloodGateOperator
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration['effects'];
    }

    public function handle(CheckingForFlooding $event)
    {
        if ($event->actor->can('postWithoutThrottle')) {
            return false;
        }

        $score = Footprint::totalScoreForUser($event->actor);

        $configuration = $this->configuration->get('Flooding');

        foreach ($configuration['thresholds'] as $level => $threshold) {
            if ($score < floatval($threshold)) {
                if (Post::where('user_id', $event->actor->id)->where('created_at', '>=', new DateTime("-{$configuration['effects'][$level]}"))->exists()) {
                    return true;
                }
            }
        }
        return !$configuration['fallback'] === true ? false : null;
    }
}
