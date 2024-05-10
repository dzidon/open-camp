<?php

namespace App\Model\EventSubscriber\Admin\GalleryImageCategory;

use App\Model\Event\Admin\GalleryImage\GalleryImageDeleteEvent;
use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryTruncateEvent;
use App\Model\Repository\GalleryImageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GalleryImageCategoryTruncateSubscriber
{
    private GalleryImageRepositoryInterface $galleryImageRepository;

    private EventDispatcherInterface $eventDispatcher;

    private EntityManagerInterface $entityManager;

    public function __construct(GalleryImageRepositoryInterface $galleryImageRepository,
                                EventDispatcherInterface        $eventDispatcher,
                                EntityManagerInterface          $entityManager)
    {
        $this->galleryImageRepository = $galleryImageRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }

    #[AsEventListener(event: GalleryImageCategoryTruncateEvent::NAME, priority: 200)]
    public function onTruncateRemoveImages(GalleryImageCategoryTruncateEvent $event): void
    {
        $data = $event->getGalleryImageCategoryTruncateData();
        $offspringsToo = $data->offspringsToo();
        $galleryImageCategory = $event->getGalleryImageCategory();
        $galleryImages = $this->galleryImageRepository->findByGalleryImageCategory($galleryImageCategory, $offspringsToo);

        foreach ($galleryImages as $galleryImage)
        {
            $deleteEvent = new GalleryImageDeleteEvent($galleryImage);
            $deleteEvent->setIsFlush(false);
            $this->eventDispatcher->dispatch($deleteEvent, $deleteEvent::NAME);
        }

        $event->setRemovedGalleryImages($galleryImages);
    }

    #[AsEventListener(event: GalleryImageCategoryTruncateEvent::NAME, priority: 100)]
    public function onTruncateFlush(GalleryImageCategoryTruncateEvent $event): void
    {
        $isFlush = $event->isFlush();

        if ($isFlush)
        {
            $this->entityManager->flush();
        }
    }
}