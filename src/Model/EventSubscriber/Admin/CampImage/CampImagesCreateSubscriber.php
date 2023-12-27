<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImagesCreateEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Service\CampImage\CampImageFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampImagesCreateSubscriber
{
    private CampImageFactoryInterface $campImageFactory;

    private CampImageRepositoryInterface $campImageRepository;

    public function __construct(CampImageFactoryInterface $campImageFactory, CampImageRepositoryInterface $repository)
    {
        $this->campImageFactory = $campImageFactory;
        $this->campImageRepository = $repository;
    }

    #[AsEventListener(event: CampImagesCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateCampImages(CampImagesCreateEvent $event): void
    {
        $data = $event->getCampImagesUploadData();
        $uploadedImages = $data->getImages();
        $camp = $data->getCamp();
        $campImages = [];

        foreach ($uploadedImages as $uploadedImage)
        {
            $campImages[] = $this->campImageFactory->createCampImage($uploadedImage, 0, $camp);
        }

        $event->setCampImages($campImages);
    }

    #[AsEventListener(event: CampImagesCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntities(CampImagesCreateEvent $event): void
    {
        $campImages = $event->getCampImages();
        $isFlush = $event->isFlush();

        foreach ($campImages as $key => $campImage)
        {
            $isLast = $key === array_key_last($campImages);
            $this->campImageRepository->saveCampImage($campImage, $isFlush && $isLast);
        }
    }
}