<?php

namespace App\Model\EventSubscriber\Admin\CampCategory;

use App\Model\Entity\CampCategory;
use App\Model\Event\Admin\CampCategory\CampCategoryCreateEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampCategoryCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private CampCategoryRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, CampCategoryRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampCategoryCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampCategoryCreateEvent $event): void
    {
        $data = $event->getCampCategoryData();
        $entity = new CampCategory($data->getName(), $data->getUrlName());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setCampCategory($entity);
    }

    #[AsEventListener(event: CampCategoryCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(CampCategoryCreateEvent $event): void
    {
        $entity = $event->getCampCategory();
        $isFlush = $event->isFlush();
        $this->repository->saveCampCategory($entity, $isFlush);
    }
}