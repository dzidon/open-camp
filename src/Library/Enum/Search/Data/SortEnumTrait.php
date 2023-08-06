<?php

namespace App\Library\Enum\Search\Data;

use LogicException;

/**
 * Trait for search sort enums.
 */
trait SortEnumTrait
{
    /**
     * Returns the property name.
     *
     * @return string
     */
    public function property(): string
    {
        return $this->getStringPart(0);
    }

    /**
     * Returns the order - ASC or DESC.
     *
     * @return string
     */
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

        if ($part === 1 && $parts[1] !== 'ASC' && $parts[1] !== 'DESC')
        {
            throw new LogicException(
                sprintf('Failed to read order using the trait "%s" in "%s". It has to be set to either "ASC" or "DESC", but it is set to "%s".', __TRAIT__, get_class($this), $parts[1])
            );
        }

        return $parts[$part];
    }
}