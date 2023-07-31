<?php

namespace App\Validator\Constraint;

use App\Validator\Validator\UniqueCampValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to any camp.
 */
#[Attribute]
class UniqueCamp extends Constraint
{
    public string $message = 'constraint.unique_camp';
    public string $urlNameProperty = 'urlName';
    public string $idProperty = 'id';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueCampValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}