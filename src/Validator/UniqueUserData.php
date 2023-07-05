<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered e-mail is not yet registered.
 */
#[Attribute]
class UniqueUserData extends Constraint
{
    public string $message = 'form.admin.user.error.not_unique';
    public string $emailProperty = 'email';
    public string $idProperty = 'id';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}