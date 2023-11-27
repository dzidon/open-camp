<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampCreatedEvent;
use App\Model\Event\Admin\Camp\CampCreateEvent;
use App\Model\Service\CampImage\CampImageFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampCreateSubscriber
{
    private CampImageFactoryInterface $campImageFactory;

    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(CampImageFactoryInterface     $campImageFactory,
                                DataTransferRegistryInterface $dataTransfer,
                                EventDispatcherInterface      $eventDispatcher)
    {
        $this->campImageFactory = $campImageFactory;
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: CampCreateEvent::NAME)]
    public function onCreateFillEntities(CampCreateEvent $event): void
    {
        $campCreationData = $event->getCampCreationData();
        $campData = $campCreationData->getCampData();

        $camp = new Camp($campData->getName(), $campData->getUrlName(), $campData->getAgeMin(), $campData->getAgeMax(), $campData->getStreet(), $campData->getTown(), $campData->getZip(), $campData->getCountry(), $campData->getPriority());
        $this->dataTransfer->fillEntity($campData, $camp);

        $campImages = [];
        $uploadedImages = $campCreationData->getImages();

        foreach ($uploadedImages as $uploadedImage)
        {
            $campImages[] = $this->campImageFactory->createCampImage($uploadedImage, 0, $camp);
        }

        $event = new CampCreatedEvent($campCreationData, $camp, $campImages);
        $this->eventDispatcher->dispatch($event, CampCreatedEvent::NAME);
    }
}