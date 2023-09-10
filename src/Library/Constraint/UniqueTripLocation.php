<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueTripLocationValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any trip location within its path.
 */
#[Attribute]
class UniqueTripLocation extends Constraint
{
    public string $message = 'unique_trip_location';
    public string $tripLocationPathProperty = 'tripLocationPath';
    public string $nameProperty = 'name';
    public string $idProperty = 'id';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueTripLocationValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}