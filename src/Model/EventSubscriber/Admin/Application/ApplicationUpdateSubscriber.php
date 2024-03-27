<?php

namespace App\Model\EventSubscriber\Admin\Application;

use App\Model\Event\Admin\Application\ApplicationUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationUpdateSubscriber
{
    private ApplicationRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationUpdateEvent $event): void
    {
        $data = $event->getApplicationData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationUpdateEvent $event): void
    {
        $entity = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->repository->saveApplication($entity, $isFlush);
    }
}