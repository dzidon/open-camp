<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImagesCreatedEvent;
use App\Model\Event\Admin\CampImage\CampImagesCreateEvent;
use App\Model\Service\CampImage\CampImageFactoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampImagesCreateSubscriber
{
    private CampImageFactoryInterface $campImageFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(CampImageFactoryInterface $campImageFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->campImageFactory = $campImageFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: CampImagesCreateEvent::NAME)]
    public function onCreateInstantiate(CampImagesCreateEvent $event): void
    {
        $data = $event->getCampImagesUploadData();
        $uploadedImages = $data->getImages();
        $camp = $data->getCamp();
        $campImages = [];

        foreach ($uploadedImages as $uploadedImage)
        {
            $campImages[] = $this->campImageFactory->createCampImage($uploadedImage, 0, $camp);
        }

        $event = new CampImagesCreatedEvent($data, $campImages);
        $this->eventDispatcher->dispatch($event, CampImagesCreatedEvent::NAME);
    }
}