<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImageUpdateEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampImageUpdateSubscriber
{
    private CampImageRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(CampImageRepositoryInterface  $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: CampImageUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(CampImageUpdateEvent $event): void
    {
        $data = $event->getCampImageData();
        $entity = $event->getCampImage();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: CampImageUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(CampImageUpdateEvent $event): void
    {
        $entity = $event->getCampImage();
        $isFlush = $event->isFlush();
        $this->repository->saveCampImage($entity, $isFlush);
    }
}