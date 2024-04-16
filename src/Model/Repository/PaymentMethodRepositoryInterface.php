<?php

namespace App\Model\Repository;

use App\Model\Entity\PaymentMethod;

interface PaymentMethodRepositoryInterface
{
    /**
     * Saves a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param bool $flush
     * @return void
     */
    public function savePaymentMethod(PaymentMethod $paymentMethod, bool $flush): void;

    /**
     * Removes a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param bool $flush
     * @return void
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod, bool $flush): void;

    /**
     * Finds all available payment methods.
     *
     * @param bool $enabledOnly
     * @param bool $includeBusinessMethods
     * @return array
     */
    public function findAll(bool $enabledOnly = false, bool $includeBusinessMethods = true): array;
}