<?php

namespace App\Model\Library\DiscountConfig;

use LogicException;

class DiscountConfigArrayShape implements DiscountConfigArrayShapeInterface
{
    public function assertRecurringCampersConfig(array $config): void
    {
        foreach ($config as $options)
        {
            if (!is_array($options))
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (count($options) > 3)
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('from', $options) || (!is_int($options['from']) && !is_null($options['from'])))
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('to', $options) || (!is_int($options['to']) && !is_null($options['to'])))
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (is_null($options['from']) && is_null($options['to']))
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (!is_null($options['from']) && !is_null($options['to']) && $options['from'] > $options['to'])
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }

            if (!array_key_exists('discount', $options) || !is_float($options['discount']))
            {
                throw $this->createInvalidRecurringCampersConfigException();
            }
        }
    }

    public function assertSiblingsConfig(array $config): void
    {
        foreach ($config as $options)
        {
            if (!is_array($options))
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (count($options) > 3)
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('from', $options) || (!is_int($options['from']) && !is_null($options['from'])))
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('to', $options) || (!is_int($options['to']) && !is_null($options['to'])))
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (is_null($options['from']) && is_null($options['to']))
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (!is_null($options['from']) && !is_null($options['to']) && $options['from'] > $options['to'])
            {
                throw $this->createInvalidSiblingsConfigException();
            }

            if (!array_key_exists('discount', $options) || !is_float($options['discount']))
            {
                throw $this->createInvalidSiblingsConfigException();
            }
        }
    }

    private function createInvalidRecurringCampersConfigException(): LogicException
    {
        return new LogicException('Sale recurring campers config array must have the following shape: [["from" => (int|null), "to" => (int|null), "discount" => (float)], ...]. Note that only one of "from" and "to" options can be null. If both "from" and "to" values are integers, "to" must be grater than "from".');
    }

    private function createInvalidSiblingsConfigException(): LogicException
    {
        return new LogicException('Siblings config array must have the following shape: [["from" => (int|null), "to" => (int|null), "discount" => (float)], ...]. Note that only one of "from" and "to" options can be null. If both "from" and "to" values are integers, "to" must be grater than "from".');
    }
}