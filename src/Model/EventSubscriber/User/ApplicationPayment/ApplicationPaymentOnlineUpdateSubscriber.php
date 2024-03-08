<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOnlineUpdateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOnlineUpdateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
    }

    #[AsEventListener(event: ApplicationPaymentOnlineUpdateEvent::NAME, priority: 200)]
    public function onUpdateSetNewState(ApplicationPaymentOnlineUpdateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();
        $newState = $event->getNewState();
        $applicationPayment->setState($newState);
    }

    #[AsEventListener(event: ApplicationPaymentOnlineUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPaymentOnlineUpdateEvent $event): void
    {
        $applicationPayment = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($applicationPayment, $isFlush);
    }
}