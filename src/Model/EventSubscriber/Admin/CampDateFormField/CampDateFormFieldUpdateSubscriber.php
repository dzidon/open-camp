<?php

namespace App\Model\EventSubscriber\Admin\CampDateFormField;

use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldUpdateEvent;
use App\Model\Repository\CampDateFormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateFormFieldUpdateSubscriber
{
    private CampDateFormFieldRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampDateFormFieldRepositoryInterface $repository,
                                DataTransferRegistryInterface        $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampDateFormFieldUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampDateFormFieldUpdateEvent $event): void
    {
        $data = $event->getCampDateFormFieldData();
        $entity = $event->getCampDateFormField();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampDateFormFieldUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampDateFormFieldUpdateEvent $event): void
    {
        $entity = $event->getCampDateFormField();
        $flush = $event->isFlush();
        $this->repository->saveCampDateFormField($entity, $flush);
    }
}