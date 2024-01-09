<?php

namespace App\Model\Service\PaymentMethod;

use App\Model\Entity\PaymentMethod;

/**
 * Creates new payment methods.
 */
interface PaymentMethodsFactoryInterface
{
    /**
     * Creates and returns new payment methods.
     *
     * @return PaymentMethod[]
     */
    public function createPaymentMethods(): array;
}