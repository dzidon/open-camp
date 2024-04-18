<?php

namespace App\Model\EventSubscriber\Admin\GalleryImageCategory;

use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryDeleteEvent;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImageCategoryDeleteSubscriber
{
    private GalleryImageCategoryRepositoryInterface $repository;

    public function __construct(GalleryImageCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: GalleryImageCategoryDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(GalleryImageCategoryDeleteEvent $event): void
    {
        $entity = $event->getGalleryImageCategory();
        $isFlush = $event->isFlush();
        $this->repository->removeGalleryImageCategory($entity, $isFlush);
    }
}