<?php

namespace App\Library\Constraint;

use App\Service\Validator\EuVatIdValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates EU VAT IDs.
 */
#[Attribute]
class EuVatId extends Constraint
{
    public string $message = 'eu_vat_id';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return EuVatIdValidator::class;
    }
}