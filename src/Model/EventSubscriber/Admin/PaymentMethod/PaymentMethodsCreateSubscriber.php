<?php

namespace App\Model\EventSubscriber\Admin\PaymentMethod;

use App\Model\Event\Admin\PaymentMethod\PaymentMethodsCreateEvent;
use App\Model\Repository\PaymentMethodRepositoryInterface;
use App\Model\Service\PaymentMethod\PaymentMethodsFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PaymentMethodsCreateSubscriber
{
    private PaymentMethodsFactoryInterface $paymentMethodsFactoryInterface;

    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    public function __construct(PaymentMethodsFactoryInterface   $paymentMethodsFactoryInterface,
                                PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodsFactoryInterface = $paymentMethodsFactoryInterface;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    #[AsEventListener(event: PaymentMethodsCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiate(PaymentMethodsCreateEvent $event): void
    {
        $paymentMethods = $this->paymentMethodsFactoryInterface->createPaymentMethods();
        $event->setPaymentMethods($paymentMethods);
    }

    #[AsEventListener(event: PaymentMethodsCreateEvent::NAME, priority: 100)]
    public function onCreateSavePermissionsAndGroups(PaymentMethodsCreateEvent $event): void
    {
        $paymentMethods = $event->getPaymentMethods();
        $isFlush = $event->isFlush();

        foreach ($paymentMethods as $key => $paymentMethod)
        {
            $isLast = $key === array_key_last($paymentMethods);
            $this->paymentMethodRepository->savePaymentMethod($paymentMethod, $isFlush && $isLast);
        }
    }
}