<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationFormFieldValueValidator;
use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Validates the value of a application form field.
 */
#[Attribute]
class ApplicationFormFieldValue extends Constraint
{
    public string $typeProperty = 'type';
    public string $optionsProperty = 'options';
    public string $valueProperty = 'value';

    #[HasNamedArguments]
    public function __construct(string $typeProperty = null,
                                string $optionsProperty = null,
                                string $valueProperty = null,
                                array $groups = null,
                                mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->typeProperty = $typeProperty ?? $this->typeProperty;
        $this->optionsProperty = $optionsProperty ?? $this->optionsProperty;
        $this->valueProperty = $valueProperty ?? $this->valueProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationFormFieldValueValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}