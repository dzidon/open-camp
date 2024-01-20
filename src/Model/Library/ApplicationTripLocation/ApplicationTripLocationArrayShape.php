<?php

namespace App\Model\Library\ApplicationTripLocation;

use LogicException;

/**
 * @inheritDoc
 */
class ApplicationTripLocationArrayShape implements ApplicationTripLocationArrayShapeInterface
{
    /**
     * @inheritDoc
     */
    public function assertLocationArrayShape(mixed $location): void
    {
        if (!is_array($location))
        {
            throw $this->createInvalidLocationInCollectionException();
        }

        if (count($location) > 2)
        {
            throw $this->createInvalidLocationInCollectionException();
        }

        if (!array_key_exists('price', $location) || !is_float($location['price']))
        {
            throw $this->createInvalidLocationInCollectionException();
        }

        if (!array_key_exists('name', $location) || !is_string($location['name']))
        {
            throw $this->createInvalidLocationInCollectionException();
        }
    }

    private function createInvalidLocationInCollectionException(): LogicException
    {
        return new LogicException('Trip location array representation must have the following shape: ["price" => (float), "name" => (string)].');
    }
}