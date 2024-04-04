<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPaymentStateConfig;

use App\Model\Entity\ApplicationPaymentStateConfig;
use App\Model\Event\Admin\ApplicationPaymentStateConfig\ApplicationPaymentStateConfigCreateEvent;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentStateConfigCreateSubscriber
{
    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    public function __construct(ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository)
    {
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
    }

    #[AsEventListener(event: ApplicationPaymentStateConfigCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePaymentStateConfig(ApplicationPaymentStateConfigCreateEvent $event): void
    {
        $states = $event->getStates();
        $paidStates = $event->getPaidStates();
        $cancelledStates = $event->getCancelledStates();
        $refundedStates = $event->getRefundedStates();
        $pendingStates = $event->getPendingStates();
        $validStateChanges = $event->getValidStateChanges();

        $applicationPayment = new ApplicationPaymentStateConfig(
            $states,
            $paidStates,
            $cancelledStates,
            $refundedStates,
            $pendingStates,
            $validStateChanges
        );

        $event->setApplicationPaymentStateConfig($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentStateConfigCreateEvent::NAME, priority: 100)]
    public function onCreateSavePaymentStateConfig(ApplicationPaymentStateConfigCreateEvent $event): void
    {
        $applicationPaymentStateConfig = $event->getApplicationPaymentStateConfig();
        $isFlush = $event->isFlush();

        $this->applicationPaymentStateConfigRepository->saveApplicationPaymentStateConfig(
            $applicationPaymentStateConfig,
            $isFlush
        );
    }
}