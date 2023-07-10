<?php

namespace App\Validator\Compound;

use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Zip code validation.
 */
#[Attribute]
class ZipCodeRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Regex('/^\d{3}(?:[\s])?\d{2}(?:(?:\s|-)\d{4})?$/'),
        ];
    }
}