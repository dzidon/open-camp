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
            $existingPaymentMethods[$existingPaymentMethod->getIdentifier()] = $existingPaymentMethod;
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
        $paymentMethods['card'] = new PaymentMethod('card', true, 300);
        $paymentMethods['transfer'] = new PaymentMethod('transfer', true, 200);
        $paymentMethods['invoice'] = new PaymentMethod('invoice', false, 100);

        return $paymentMethods;
    }
}