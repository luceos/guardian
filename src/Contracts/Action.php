<?php

namespace FoF\Guardian\Contracts;

use FoF\Guardian\Models\Footprint;

interface Action
{
    /**
     * Should the action be triggered?
     *
     * @param Footprint $footprint
     * @return bool
     */
    public function on(Footprint $footprint): bool;

    /**
     * Execute the action.
     *
     * @param Footprint $footprint
     * @return mixed
     */
    public function run(Footprint $footprint);

    /**
     * The priority of this action.
     *
     * @info higher value is higher priority.
     *
     * @return int|null
     */
    public function priority(): ?int;
}
