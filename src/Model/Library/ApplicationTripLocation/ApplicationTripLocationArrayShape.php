<?php

namespace App\Model\Library\ApplicationTripLocation;

use LogicException;

/**
 * Asserts that an array representation of ApplicationTripLocation has the required shape.
 */
class ApplicationTripLocationArrayShape
{
    /**
     * Throws a LogicException if the given array representation of ApplicationTripLocation doesn't have the required shape.
     *
     * @param mixed $location
     * @return void
     */
    public static function assertLocationArrayShape(mixed $location): void
    {
        if (!is_array($location))
        {
            throw self::createInvalidLocationInCollectionException();
        }

        if (count($location) > 2)
        {
            throw self::createInvalidLocationInCollectionException();
        }

        if (!array_key_exists('price', $location) || !is_float($location['price']))
        {
            throw self::createInvalidLocationInCollectionException();
        }

        if (!array_key_exists('name', $location) || !is_string($location['name']))
        {
            throw self::createInvalidLocationInCollectionException();
        }
    }

    private static function createInvalidLocationInCollectionException(): LogicException
    {
        return new LogicException('Trip location array representation must have the following shape: ["price" => (float), "name" => (string)].');
    }
}