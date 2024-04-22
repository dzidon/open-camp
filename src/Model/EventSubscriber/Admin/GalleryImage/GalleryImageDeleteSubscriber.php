<?php

namespace App\Model\EventSubscriber\Admin\GalleryImage;

use App\Model\Event\Admin\GalleryImage\GalleryImageDeleteEvent;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Model\Service\GalleryImage\GalleryImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImageDeleteSubscriber
{
    private GalleryImageFilesystemInterface $galleryImageFilesystem;

    private GalleryImageRepositoryInterface $repository;

    public function __construct(GalleryImageFilesystemInterface $galleryImageFilesystem,
                                GalleryImageRepositoryInterface $repository)
    {
        $this->galleryImageFilesystem = $galleryImageFilesystem;
        $this->repository = $repository;
    }

    #[AsEventListener(event: GalleryImageDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveFile(GalleryImageDeleteEvent $event): void
    {
        $entity = $event->getGalleryImage();
        $this->galleryImageFilesystem->removeFile($entity);
    }

    #[AsEventListener(event: GalleryImageDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(GalleryImageDeleteEvent $event): void
    {
        $entity = $event->getGalleryImage();
        $isFlush = $event->isFlush();
        $this->repository->removeGalleryImage($entity, $isFlush);
    }
}