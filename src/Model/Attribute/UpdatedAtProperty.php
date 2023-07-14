<?php

namespace App\Model\Attribute;

use Attribute;
use DateTime;
use DateTimeImmutable;
use LogicException;

/**
 * Attribute for "update at" date time properties.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UpdatedAtProperty
{
    const SUPPORTED_TYPES = [DateTime::class, DateTimeImmutable::class];

    private string $dateTimeType;

    public function __construct(string $dateTimeType)
    {
        if (!in_array($dateTimeType, self::SUPPORTED_TYPES))
        {
            throw new LogicException(
                sprintf('Parameter "dateTimeType" in attribute "%s" only supports "%s", but "%s" was passed.', UpdatedAtProperty::class, implode('", or "', self::SUPPORTED_TYPES), $dateTimeType)
            );
        }

        $this->dateTimeType = $dateTimeType;
    }

    public function getDateTimeType(): string
    {
        return $this->dateTimeType;
    }
}