<?php

namespace App\Library\Constraint;

use App\Service\Validator\EuCinValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates EU CIN numbers.
 */
#[Attribute]
class EuCin extends Constraint
{
    public string $message = 'eu_cin';

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
        return EuCinValidator::class;
    }
}