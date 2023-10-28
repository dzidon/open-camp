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
    public string $tripLocationProperty = 'tripLocation';
    public string $nameProperty = 'name';

    public function __construct(string $message = null,
                                string $tripLocationPathProperty = null,
                                string $tripLocationProperty = null,
                                string $nameProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->tripLocationPathProperty = $tripLocationPathProperty ?? $this->tripLocationPathProperty;
        $this->tripLocationProperty = $tripLocationProperty ?? $this->tripLocationProperty;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
    }

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