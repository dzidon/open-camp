<?php

namespace App\Model\EventSubscriber\Admin\CampDateFormField;

use App\Model\Entity\CampDateFormField;
use App\Model\Event\Admin\CampDateFormField\CampDateFormFieldCreateEvent;
use App\Model\Repository\CampDateFormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateFormFieldCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private CampDateFormFieldRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CampDateFormFieldRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateFormFieldCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampDateFormFieldCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $data = $event->getCampDateFormFieldData();
        $entity = new CampDateFormField($campDate, $data->getFormField(), $data->getPriority());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampDateFormField($entity);
    }

    #[AsEventListener(event: CampDateFormFieldCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampDateFormFieldCreateEvent $event): void
    {
        $entity = $event->getCampDateFormField();
        $flush = $event->isFlush();
        $this->repository->saveCampDateFormField($entity, $flush);
    }
}