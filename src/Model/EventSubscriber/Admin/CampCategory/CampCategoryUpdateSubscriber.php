<?php

namespace App\Model\EventSubscriber\Admin\CampCategory;

use App\Model\Event\Admin\CampCategory\CampCategoryUpdateEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampCategoryUpdateSubscriber
{
    private CampCategoryRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampCategoryRepositoryInterface $repository,
                                DataTransferRegistryInterface   $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampCategoryUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampCategoryUpdateEvent $event): void
    {
        $data = $event->getCampCategoryData();
        $entity = $event->getCampCategory();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampCategoryUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampCategoryUpdateEvent $event): void
    {
        $entity = $event->getCampCategory();
        $this->repository->saveCampCategory($entity, true);
    }
}