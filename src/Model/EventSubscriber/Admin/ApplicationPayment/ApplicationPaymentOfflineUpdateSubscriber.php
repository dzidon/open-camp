<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineUpdateEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineUpdateSubscriber
{
    private ApplicationPaymentRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPaymentRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationPaymentOfflineUpdateEvent $event): void
    {
        $data = $event->getApplicationPaymentData();
        $entity = $event->getApplicationPayment();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationPaymentOfflineUpdateEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationPayment($entity, $isFlush);
    }
}