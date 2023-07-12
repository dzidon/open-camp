<?php

namespace App\Validator\Constraint;

use App\Validator\Validator\UniqueUserValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered e-mail is not yet registered.
 */
#[Attribute]
class UniqueUser extends Constraint
{
    public string $message = 'constraint.unique_user';
    public string $emailProperty = 'email';
    public string $idProperty = 'id';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueUserValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}