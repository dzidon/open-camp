<?php

namespace App\Model\Event\Admin\PaymentMethod;

use App\Model\Entity\PaymentMethod;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class PaymentMethodsCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.payment_methods.create';

    /** @var PaymentMethod[] */
    private array $paymentMethods = [];

    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    public function setPaymentMethods(array $paymentMethods): self
    {
        foreach ($paymentMethods as $paymentMethod)
        {
            if (!$paymentMethod instanceof PaymentMethod)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, PaymentMethod::class)
                );
            }
        }

        $this->paymentMethods = $paymentMethods;

        return $this;
    }
}