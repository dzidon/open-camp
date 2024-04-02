<?php

namespace App\Model\EventSubscriber\Admin\ApplicationTripLocationPath;

use App\Model\Entity\ApplicationTripLocationPath;
use App\Model\Event\Admin\ApplicationTripLocationPath\ApplicationTripLocationPathCreateEvent;
use App\Model\Repository\ApplicationTripLocationPathRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationTripLocationPathCreateSubscriber
{
    private ApplicationTripLocationPathRepositoryInterface $applicationTripLocationPathRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationTripLocationPathRepositoryInterface $applicationTripLocationPathRepository,
                                DataTransferRegistryInterface                  $dataTransfer)
    {
        $this->applicationTripLocationPathRepository = $applicationTripLocationPathRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationTripLocationPathCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationTripLocationPathCreateEvent $event): void
    {
        $data = $event->getApplicationCamperData();
        $applicationCamper = $event->getApplicationCamper();
        $isThere = $event->isThere();

        if ($isThere)
        {
            $locations = $data->getTripLocationsThere();
            $location = $data->getTripLocationThere();
        }
        else
        {
            $locations = $data->getTripLocationsBack();
            $location = $data->getTripLocationBack();
        }

        $tripLocationPathThere = new ApplicationTripLocationPath($isThere, $locations, $location, $applicationCamper);
        $this->dataTransfer->fillEntity($data, $tripLocationPathThere);
        $event->setApplicationTripLocationPath($tripLocationPathThere);
    }

    #[AsEventListener(event: ApplicationTripLocationPathCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationTripLocationPathCreateEvent $event): void
    {
        $applicationTripLocationPath = $event->getApplicationTripLocationPath();
        $isFlush = $event->isFlush();
        $this->applicationTripLocationPathRepository->saveApplicationTripLocationPath($applicationTripLocationPath, $isFlush);
    }
}