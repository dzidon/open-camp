<?php

namespace App\Model\Library\DiscountConfig;

use LogicException;

class DiscountConfigArrayValidator
{
    public static function assertRecurringCampersConfig(array $config): void
    {
        foreach ($config as $options)
        {
            if (!is_array($options))
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (count($options) > 3)
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('from', $options) || (!is_int($options['from']) && !is_null($options['from'])))
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('to', $options) || (!is_int($options['to']) && !is_null($options['to'])))
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (is_null($options['from']) && is_null($options['to']))
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (!is_null($options['from']) && !is_null($options['to']) && $options['from'] > $options['to'])
            {
                throw self::createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('discount', $options) || !is_float($options['discount']))
            {
                throw self::createInvalidRecurringCampersConfigException();
            }
        }
    }

    public static function assertSiblingsConfig(array $config): void
    {
        foreach ($config as $options)
        {
            if (!is_array($options))
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (count($options) > 3)
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('from', $options) || (!is_int($options['from']) && !is_null($options['from'])))
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('to', $options) || (!is_int($options['to']) && !is_null($options['to'])))
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (is_null($options['from']) && is_null($options['to']))
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (!is_null($options['from']) && !is_null($options['to']) && $options['from'] > $options['to'])
            {
                throw self::createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('discount', $options) || !is_float($options['discount']))
            {
                throw self::createInvalidSiblingsConfigException();
            }
        }
    }

    public static function isSiblingDiscountIntervalEligibleForNumberOfCampers(?int $discountSiblingsIntervalFrom,
                                                                               ?int $discountSiblingsIntervalTo,
                                                                               int  $numberOfApplicationCampers): bool
    {
        if ($discountSiblingsIntervalTo !== null && $numberOfApplicationCampers > $discountSiblingsIntervalTo)
        {
            return true;
        }

        return $discountSiblingsIntervalFrom === null || $numberOfApplicationCampers >= $discountSiblingsIntervalFrom;
    }

    private static function createInvalidRecurringCampersConfigException(): LogicException
    {
        return new LogicException('Sale recurring campers config array must have the following shape: [["from" => (int|null), "to" => (int|null), "discount" => (float)], ...]. Note that only one of "from" and "to" options can be null. If both "from" and "to" values are integers, "to" must be grater than "from".');
    }

    private static function createInvalidSiblingsConfigException(): LogicException
    {
        return new LogicException('Siblings config array must have the following shape: [["from" => (int|null), "to" => (int|null), "discount" => (float)], ...]. Note that only one of "from" and "to" options can be null. If both "from" and "to" values are integers, "to" must be grater than "from".');
    }
}