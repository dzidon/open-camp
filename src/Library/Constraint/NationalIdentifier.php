<?php

namespace App\Library\Constraint;

use App\Service\Validator\NationalIdentifierValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates national identifiers.
 */
#[Attribute]
class NationalIdentifier extends Constraint
{
    public string $message = 'national_identifier';

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
        return NationalIdentifierValidator::class;
    }
}