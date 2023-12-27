<?php

namespace App\Model\Library\ApplicationTripLocation;

/**
 * Asserts that an array representation of ApplicationTripLocation has the required shape.
 */
interface ApplicationTripLocationArrayShapeInterface
{
    /**
     * Throws a LogicException if the given array representation of ApplicationTripLocation doesn't have the required shape.
     *
     * @param mixed $location
     * @return void
     */
    public function assertLocationArrayShape(mixed $location): void;
}