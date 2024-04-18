<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineCreateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate;

    public function __construct(DataTransferRegistryInterface          $dataTransfer,
                                ApplicationPaymentRepositoryInterface  $applicationPaymentRepository,
                                ApplicationPaymentOfflineGateInterface $applicationPaymentOfflineGate)
    {
        $this->dataTransfer = $dataTransfer;
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOfflineGate = $applicationPaymentOfflineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationPaymentOfflineCreateEvent $event): void
    {
        $application = $event->getApplication();
        $data = $event->getApplicationPaymentData();
        $type = $data->getType();
        $applicationPayment = $this->applicationPaymentOfflineGate->createOfflinePayment($type, $application);
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