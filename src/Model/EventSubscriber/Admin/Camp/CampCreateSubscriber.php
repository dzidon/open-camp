<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Library\Data\Admin\CampImagesUploadData;
use App\Model\Entity\Camp;
use App\Model\Event\Admin\Camp\CampCreateEvent;
use App\Model\Event\Admin\CampImage\CampImagesCreateEvent;
use App\Model\Repository\CampRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CampCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    private CampRepositoryInterface $campRepository;

    public function __construct(DataTransferRegistryInterface $dataTransfer,
                                EventDispatcherInterface      $eventDispatcher,
                                CampRepositoryInterface       $campRepository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
        $this->campRepository = $campRepository;
    }

    #[AsEventListener(event: CampCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(CampCreateEvent $event): void
    {
        $campCreationData = $event->getCampCreationData();
        $uploadedImages = $campCreationData->getImages();
        $campData = $campCreationData->getCampData();

        $camp = new Camp($campData->getName(), $campData->getUrlName(), $campData->getAgeMin(), $campData->getAgeMax(), $campData->getStreet(), $campData->getTown(), $campData->getZip(), $campData->getCountry(), $campData->getPriority());
        $this->dataTransfer->fillEntity($campData, $camp);
        $event->setCamp($camp);

        $uploadData = new CampImagesUploadData($camp);
        $uploadData->setImages($uploadedImages);
        $uploadEvent = new CampImagesCreateEvent($uploadData);
        $uploadEvent->setIsFlush(false);
        $this->eventDispatcher->dispatch($uploadEvent, $uploadEvent::NAME);
    }

    #[AsEventListener(event: CampCreateEvent::NAME, priority: 100)]
    public function onCreateSave(CampCreateEvent $event): void
    {
        $camp = $event->getCamp();
        $isFlush = $event->isFlush();
        $this->campRepository->saveCamp($camp, $isFlush);
    }
}