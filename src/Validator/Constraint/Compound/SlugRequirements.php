<?php

namespace App\Validator\Constraint\Compound;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Url slug validation.
 */
#[Attribute]
class SlugRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Regex('/^([a-zA-Z0-9-])*$/'),
        ];
    }
}