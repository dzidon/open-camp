<?php

namespace App\Model\Library\DiscountConfig;

interface DiscountSiblingsIntervalShapeInterface
{
    public function assertDiscountSiblingsInterval(array $interval): void;
}