<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineDepositCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineDepositCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    public function __construct(ApplicationPaymentRepositoryInterface  $applicationPaymentRepository,
                                ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineDepositCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOfflineDepositCreateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationPayment = $this->applicationPaymentOfflineGate->createOfflineDepositPayment($application);
        $event->setApplicationPayment($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineDepositCreateEvent::NAME, priority: 100)]
    public function onCreateSavePayment(ApplicationPaymentOfflineDepositCreateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($applicationPayment, $isFlush);
    }
}