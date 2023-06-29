<?php

namespace App\Enum\Search\Data;

use LogicException;

/**
 * Trait for search sort enums.
 */
trait SortEnumTrait
{
    public function property(): string
    {
        return $this->getStringPart(0);
    }

    public function order(): string
    {
        return $this->getStringPart(1);
    }

    private function getStringPart(int $part): string
    {
        if (!isset($this->value))
        {
            throw new LogicException(
                sprintf('Trait "%s" cannot be used, because there is no public "value" attribute in "%s".', __TRAIT__, get_class($this))
            );
        }

        $partNames = [
            0 => 'property',
            1 => 'order',
        ];

        $value = trim(preg_replace('/\s\s+/', ' ', $this->value));
        $parts = explode(' ', $value);

        if (count($parts) !== 2 || !array_key_exists($part, $parts))
        {
            throw new LogicException(
                sprintf('Failed to read %s name using the trait "%s" in "%s".', $partNames[$part], __TRAIT__, get_class($this))
            );
        }

        return $parts[$part];
    }
}