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

    public function __construct(string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return EuVatIdValidator::class;
    }
}