<?php

namespace App\Model\Library\DiscountConfig;

use LogicException;

class DiscountSiblingsIntervalShape
{
    public static function assertDiscountSiblingsInterval(array $interval): void
    {
        $exception = new LogicException('Discount siblings interval must contain a combination of two integers, or a combination of an integer and a null value. The two integers must be indexed as "from" and "to"');

        if (count($interval) !== 2)
        {
            throw new $exception;
        }

        if (!array_key_exists('from', $interval))
        {
            throw new $exception;
        }

        if (!array_key_exists('to', $interval))
        {
            throw new $exception;
        }

        $from = $interval['from'];
        $to = $interval['to'];

        if ($from === null && $to === null)
        {
            throw new $exception;
        }

        if ($from !== null && !is_int($from))
        {
            throw new $exception;
        }

        if ($to !== null && !is_int($to))
        {
            throw new $exception;
        }
    }
}