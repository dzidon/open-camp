<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueElementsInArrayValidator;
use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that an array contains unique objects or sub-arrays.
 */
#[Attribute]
class UniqueElementsInArray extends Constraint
{
    public array $fields;
    public string $message = 'unique_elements_in_array';

    #[HasNamedArguments]
    public function __construct(array $fields, string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->fields = $fields;
        $this->message = $message ?? $this->message;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueElementsInArrayValidator::class;
    }
}