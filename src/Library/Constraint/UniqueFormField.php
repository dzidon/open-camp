<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueFormFieldValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to form field.
 */
#[Attribute]
class UniqueFormField extends Constraint
{
    public string $message = 'unique_form_field';
    public string $nameProperty = 'name';
    public string $formFieldProperty = 'formField';

    public function __construct(string $message = null,
                                string $nameProperty = null,
                                string $formFieldProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
        $this->formFieldProperty = $formFieldProperty ?? $this->formFieldProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueFormFieldValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}