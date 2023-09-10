<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueTripLocationsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any trip location within its path. There is no validation
 * against the database.
 */
#[Attribute]
class UniqueTripLocations extends Constraint
{
    public string $message = 'unique_trip_location';
    public string $tripLocationsDataProperty = 'tripLocationsData';
    public string $nameProperty = 'name';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueTripLocationsValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}