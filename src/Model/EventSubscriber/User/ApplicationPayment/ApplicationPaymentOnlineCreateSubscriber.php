<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOnlineCreateEvent;
use App\Model\Event\User\ApplicationPaymentStateConfig\ApplicationPaymentStateConfigCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Online\ApplicationPaymentOnlineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationPaymentOnlineCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    private ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationPaymentRepositoryInterface            $applicationPaymentRepository,
                                ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository,
                                ApplicationPaymentOnlineGateInterface            $applicationPaymentOnlineGate,
                                EventDispatcherInterface                         $eventDispatcher)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
        $this->applicationPaymentOnlineGate = $applicationPaymentOnlineGate;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationPaymentOnlineCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOnlineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $type = $event->getType();

        $states = $this->applicationPaymentOnlineGate->getStates();
        $paidStates = $this->applicationPaymentOnlineGate->getPaidStates();
        $cancelledStates = $this->applicationPaymentOnlineGate->getCancelledStates();
        $refundedStates = $this->applicationPaymentOnlineGate->getRefundedStates();
        $pendingStates = $this->applicationPaymentOnlineGate->getPendingStates();
        $validStateChanges = $this->applicationPaymentOnlineGate->getValidStateChanges();

        $applicationPaymentStateConfig = $this->applicationPaymentStateConfigRepository->findOneByConfiguration(
            $states,
            $paidStates,
            $cancelledStates,
            $refundedStates,
            $pendingStates,
            $validStateChanges,
        );

        if ($applicationPaymentStateConfig === null)
        {
            $stateConfigEvent = new ApplicationPaymentStateConfigCreateEvent(
                $states,
                $paidStates,
                $cancelledStates,
                $refundedStates,
                $pendingStates,
                $validStateChanges,
            );

            $stateConfigEvent->setIsFlush(false);
            $this->eventDispatcher->dispatch($stateConfigEvent, $stateConfigEvent::NAME);
            $applicationPaymentStateConfig = $stateConfigEvent->getApplicationPaymentStateConfig();
        }

        $applicationPayment = $this->applicationPaymentOnlineGate->createOnlinePayment(
            $type,
            $application,
            $applicationPaymentStateConfig
        );

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