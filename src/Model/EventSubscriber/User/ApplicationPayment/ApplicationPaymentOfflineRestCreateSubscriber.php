<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineRestCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineRestCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    public function __construct(ApplicationPaymentRepositoryInterface  $applicationPaymentRepository,
                                ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineRestCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOfflineRestCreateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationPayment = $this->applicationPaymentOfflineGate->createOfflineRestPayment($application);
        $event->setApplicationPayment($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineRestCreateEvent::NAME, priority: 100)]
    public function onCreateSavePayment(ApplicationPaymentOfflineRestCreateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($applicationPayment, $isFlush);
    }
}