<?php

namespace App\Model\EventSubscriber\Admin\ApplicationContact;

use App\Model\Event\Admin\ApplicationContact\ApplicationContactUpdateEvent;
use App\Model\Repository\ApplicationContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationContactUpdateSubscriber
{
    private ApplicationContactRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationContactRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationContactUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationContactUpdateEvent $event): void
    {
        $data = $event->getContactData();
        $entity = $event->getApplicationContact();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationContactUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationContactUpdateEvent $event): void
    {
        $entity = $event->getApplicationContact();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationContact($entity, $isFlush);
    }
}