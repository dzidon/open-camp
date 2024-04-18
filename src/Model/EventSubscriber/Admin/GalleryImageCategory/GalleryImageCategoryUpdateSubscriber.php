<?php

namespace App\Model\EventSubscriber\Admin\GalleryImageCategory;

use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryUpdateEvent;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImageCategoryUpdateSubscriber
{
    private GalleryImageCategoryRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(GalleryImageCategoryRepositoryInterface $repository,
                                DataTransferRegistryInterface   $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: GalleryImageCategoryUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(GalleryImageCategoryUpdateEvent $event): void
    {
        $data = $event->getGalleryImageCategoryData();
        $entity = $event->getGalleryImageCategory();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: GalleryImageCategoryUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(GalleryImageCategoryUpdateEvent $event): void
    {
        $entity = $event->getGalleryImageCategory();
        $isFlush = $event->isFlush();
        $this->repository->saveGalleryImageCategory($entity, $isFlush);
    }
}