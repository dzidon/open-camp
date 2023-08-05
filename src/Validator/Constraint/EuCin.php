<?php

namespace App\Validator\Constraint;

use App\Validator\Validator\EuCinValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates EU CIN numbers.
 */
#[Attribute]
class EuCin extends Constraint
{
    public string $message = 'eu_cin';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return EuCinValidator::class;
    }
}