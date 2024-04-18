<?php

namespace App\Model\EventSubscriber\Admin\GalleryImageCategory;

use App\Model\Entity\GalleryImageCategory;
use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryCreateEvent;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImageCategoryCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private GalleryImageCategoryRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, GalleryImageCategoryRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: GalleryImageCategoryCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(GalleryImageCategoryCreateEvent $event): void
    {
        $data = $event->getGalleryImageCategoryData();
        $entity = new GalleryImageCategory($data->getName(), $data->getUrlName());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setGalleryImageCategory($entity);
    }

    #[AsEventListener(event: GalleryImageCategoryCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(GalleryImageCategoryCreateEvent $event): void
    {
        $entity = $event->getGalleryImageCategory();
        $isFlush = $event->isFlush();
        $this->repository->saveGalleryImageCategory($entity, $isFlush);
    }
}