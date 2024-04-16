<?php

namespace App\Model\Service\PaymentMethod;

use App\Model\Entity\PaymentMethod;
use App\Model\Repository\PaymentMethodRepositoryInterface;

/**
 * @inheritDoc
 */
class PaymentMethodsFactory implements PaymentMethodsFactoryInterface
{
    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @inheritDoc
     */
    public function createPaymentMethods(): array
    {
        // load existing
        $existingPaymentMethods = [];

        foreach ($this->paymentMethodRepository->findAll() as $existingPaymentMethod)
        {
            $existingPaymentMethods[$existingPaymentMethod->getName()] = $existingPaymentMethod;
        }

        // create new
        $createdPaymentMethods = [];
        $possiblePaymentMethods = $this->instantiatePaymentMethods();

        foreach ($possiblePaymentMethods as $identifier => $paymentMethod)
        {
            if (!array_key_exists($identifier, $existingPaymentMethods))
            {
                $createdPaymentMethods[] = $paymentMethod;
            }
        }

        return $createdPaymentMethods;
    }

    /**
     * @return PaymentMethod[]
     */
    private function instantiatePaymentMethods(): array
    {
        $paymentMethods['card'] = new PaymentMethod('card', 'payment_method.card', true, false, 300);
        $paymentMethods['transfer'] = new PaymentMethod('transfer', 'payment_method.transfer', true, false, 200);
        $paymentMethods['invoice'] = new PaymentMethod('invoice', 'payment_method.invoice', false, true, 100);

        return $paymentMethods;
    }
}