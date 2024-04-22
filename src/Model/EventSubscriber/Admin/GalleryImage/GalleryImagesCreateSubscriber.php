<?php

namespace App\Model\EventSubscriber\Admin\GalleryImage;

use App\Model\Event\Admin\GalleryImage\GalleryImagesCreateEvent;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Model\Service\GalleryImage\GalleryImageFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class GalleryImagesCreateSubscriber
{
    private GalleryImageFactoryInterface $galleryImageFactory;

    private GalleryImageRepositoryInterface $galleryImageRepository;

    public function __construct(GalleryImageFactoryInterface    $galleryImageFactory,
                                GalleryImageRepositoryInterface $repository)
    {
        $this->galleryImageFactory = $galleryImageFactory;
        $this->galleryImageRepository = $repository;
    }

    #[AsEventListener(event: GalleryImagesCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateGalleryImages(GalleryImagesCreateEvent $event): void
    {
        $data = $event->getGalleryImagesUploadData();
        $uploadedImages = $data->getImages();
        $galleryImageCategory = $data->getGalleryImageCategory();
        $isHiddenInGallery = $data->isHiddenInGallery();
        $isInCarousel = $data->isInCarousel();
        $galleryImages = [];

        foreach ($uploadedImages as $uploadedImage)
        {
            $galleryImage = $this->galleryImageFactory->createGalleryImage($uploadedImage);
            $galleryImage->setGalleryImageCategory($galleryImageCategory);
            $galleryImage->setIsHiddenInGallery($isHiddenInGallery);
            $galleryImage->setIsInCarousel($isInCarousel);

            if ($isInCarousel)
            {
                $galleryImage->setCarouselPriority(0);
            }

            $galleryImages[] = $galleryImage;
        }

        $event->setGalleryImages($galleryImages);
    }

    #[AsEventListener(event: GalleryImagesCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntities(GalleryImagesCreateEvent $event): void
    {
        $galleryImages = $event->getGalleryImages();
        $isFlush = $event->isFlush();

        foreach ($galleryImages as $key => $galleryImage)
        {
            $isLast = $key === array_key_last($galleryImages);
            $this->galleryImageRepository->saveGalleryImage($galleryImage, $isFlush && $isLast);
        }
    }
}