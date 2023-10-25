<?php

namespace App\Library\Constraint\Compound;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Validation of custom form field choice item.
 */
#[Attribute]
class FormFieldChoiceItemRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Length(max: 255),
            new Assert\NotBlank(),
        ];
    }
}