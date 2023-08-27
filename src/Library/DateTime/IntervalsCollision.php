<?php

namespace App\Library\DateTime;

use DateTimeInterface;

/**
 * Checks for collision of two date time intervals.
 */
class IntervalsCollision implements IntervalsCollisionInterface
{
    private bool $isFound;

    public function __construct(DateTimeInterface $from1, DateTimeInterface $to1,
                                DateTimeInterface $from2, DateTimeInterface $to2,
                                bool $closedInterval = true)
    {
        $this->isFound = $this->dateTimeIntervalsCollide($from1, $to1, $from2, $to2, $closedInterval);
    }

    /**
     * @inheritDoc
     */
    public function isFound(): bool
    {
        return $this->isFound;
    }

    /**
     * Returns true if two date time intervals collide.
     *
     * @param DateTimeInterface $from1
     * @param DateTimeInterface $to1
     * @param DateTimeInterface $from2
     * @param DateTimeInterface $to2
     * @param bool $closedInterval Should true be returned, when start and end date times are equal?
     * @return bool
     */
    private function dateTimeIntervalsCollide(DateTimeInterface $from1, DateTimeInterface $to1,
                                              DateTimeInterface $from2, DateTimeInterface $to2,
                                              bool $closedInterval = true): bool
    {
        if ($closedInterval)
        {
            if ($from1 >= $from2 && $from1 <= $to2)
            {
                return true;
            }

            if ($to1 >= $from2 && $to1 <= $to2)
            {
                return true;
            }
        }
        else
        {
            if ($from1 >= $from2 && $from1 < $to2)
            {
                return true;
            }

            if ($to1 > $from2 && $to1 <= $to2)
            {
                return true;
            }
        }

        if ($from2 >= $from1 && $to2 <= $to1)
        {
            return true;
        }

        return false;
    }
}