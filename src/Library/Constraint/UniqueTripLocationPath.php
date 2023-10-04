<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueTripLocationPathValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any trip location path.
 */
#[Attribute]
class UniqueTripLocationPath extends Constraint
{
    public string $message = 'unique_trip_location_path';
    public string $nameProperty = 'name';
    public string $idProperty = 'id';

    public function __construct(string $message = null,
                                string $nameProperty = null,
                                string $idProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
        $this->idProperty = $idProperty ?? $this->idProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueTripLocationPathValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}