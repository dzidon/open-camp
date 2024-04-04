<?php

namespace App\Model\EventSubscriber\User\ApplicationPayment;

use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineCreateEvent;
use App\Model\Event\User\ApplicationPaymentStateConfig\ApplicationPaymentStateConfigCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationPaymentOfflineCreateSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationPaymentRepositoryInterface            $applicationPaymentRepository,
                                ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository,
                                ApplicationPaymentOfflineGateInterface           $applicationPaymentOfflineGate,
                                EventDispatcherInterface                         $eventDispatcher)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiatePayment(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $type = $event->getType();
        $states = $this->applicationPaymentOfflineGate->getStates();
        $initialState = $this->applicationPaymentOfflineGate->getInitialState();
        $paidStates = $this->applicationPaymentOfflineGate->getPaidStates();
        $cancelledStates = $this->applicationPaymentOfflineGate->getCancelledStates();
        $refundedStates = $this->applicationPaymentOfflineGate->getRefundedStates();
        $pendingStates = $this->applicationPaymentOfflineGate->getPendingStates();
        $validStateChanges = $this->applicationPaymentOfflineGate->getValidStateChanges();

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

        $amount = match ($type) {
            ApplicationPaymentTypeEnum::DEPOSIT => $application->getFullDeposit(),
            ApplicationPaymentTypeEnum::REST    => $application->getFullRest(),
            ApplicationPaymentTypeEnum::FULL    => $application->getFullPrice(),
        };

        $applicationPayment = new ApplicationPayment(
            $amount,
            $type,
            $initialState,
            false,
            $applicationPaymentStateConfig,
            $application,
        );

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