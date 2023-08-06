<?php

namespace App\Library\Constraint\Compound;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Street and house number validation.
 */
#[Attribute]
class StreetRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Regex('/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*\s).+$/'),
        ];
    }
}