<?php

namespace App\Validator\Constraint;

use App\Validator\Validator\NationalIdentifierValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates national identifiers.
 */
#[Attribute]
class NationalIdentifier extends Constraint
{
    public string $message = 'national_identifier';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return NationalIdentifierValidator::class;
    }
}