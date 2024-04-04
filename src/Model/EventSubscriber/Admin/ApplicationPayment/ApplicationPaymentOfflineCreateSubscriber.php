<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Entity\ApplicationPayment;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineCreateEvent;
use App\Model\Event\Admin\ApplicationPaymentStateConfig\ApplicationPaymentStateConfigCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationPaymentOfflineCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface                    $dataTransfer,
                                ApplicationPaymentRepositoryInterface            $applicationPaymentRepository,
                                ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository,
                                ApplicationPaymentOfflineGateInterface           $applicationPaymentOfflineGate,
                                EventDispatcherInterface                         $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $data = $event->getApplicationPaymentData();
        $amount = $data->getAmount();
        $type = $data->getType();
        $state = $data->getState();

        $states = $this->applicationPaymentOfflineGate->getStates();
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

        $applicationPayment = new ApplicationPayment(
            $amount,
            $type,
            $state,
            false,
            $applicationPaymentStateConfig,
            $application,
        );

        $this->dataTransfer->fillEntity($data, $applicationPayment);
        $event->setApplicationPayment($applicationPayment);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($entity, $isFlush);
    }
}