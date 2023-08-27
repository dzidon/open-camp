<?php

namespace App\Library\DateTime;

/**
 * Checks for collision of two time intervals.
 */
interface IntervalsCollisionInterface
{
    /**
     * Returns true if a collision is found.
     *
     * @return bool
     */
    public function isFound(): bool;
}