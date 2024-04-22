<?php

namespace App\Model\EventSubscriber\Admin\GalleryImage;

use App\Model\Event\Admin\GalleryImage\GalleryImageUpdateEvent;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImageUpdateSubscriber
{
    private GalleryImageRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(GalleryImageRepositoryInterface  $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: GalleryImageUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(GalleryImageUpdateEvent $event): void
    {
        $data = $event->getGalleryImageData();
        $entity = $event->getGalleryImage();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: GalleryImageUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(GalleryImageUpdateEvent $event): void
    {
        $entity = $event->getGalleryImage();
        $isFlush = $event->isFlush();
        $this->repository->saveGalleryImage($entity, $isFlush);
    }
}