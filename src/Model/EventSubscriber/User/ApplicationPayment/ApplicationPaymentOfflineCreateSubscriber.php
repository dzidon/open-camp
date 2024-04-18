<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    public function __construct(ApplicationPaymentRepositoryInterface  $applicationPaymentRepository,
                                ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $type = $event->getType();
        $applicationPayment = $this->applicationPaymentOfflineGate->createOfflinePayment($type, $application);
        $event->setApplicationPayment($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 100)]
    public function onCreateSavePayment(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($applicationPayment, $isFlush);
    }
}