<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOnlineCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Online\ApplicationPaymentOnlineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOnlineCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository,
                                ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOnlineGate = $applicationPaymentOnlineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOnlineCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOnlineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $type = $event->getType();
        $applicationPayment = $this->applicationPaymentOnlineGate->createOnlinePayment($type, $application);
        $event->setApplicationPayment($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentOnlineCreateEvent::NAME, priority: 100)]
    public function onCreateSavePayment(ApplicationPaymentOnlineCreateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();

        if ($applicationPayment === null)
        {
            return;
        }

        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($applicationPayment, $isFlush);
    }
}