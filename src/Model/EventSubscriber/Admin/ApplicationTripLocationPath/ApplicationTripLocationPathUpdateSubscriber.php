<?php

namespace App\Model\EventSubscriber\Admin\ApplicationTripLocationPath;

use App\Model\Event\Admin\ApplicationTripLocationPath\ApplicationTripLocationPathUpdateEvent;
use App\Model\Repository\ApplicationTripLocationPathRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationTripLocationPathUpdateSubscriber
{
    private ApplicationTripLocationPathRepositoryInterface $applicationTripLocationPathRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationTripLocationPathRepositoryInterface $applicationTripLocationPathRepository,
                                DataTransferRegistryInterface                  $dataTransfer)
    {
        $this->applicationTripLocationPathRepository = $applicationTripLocationPathRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationTripLocationPathUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationTripLocationPathUpdateEvent $event): void
    {
        $data = $event->getApplicationCamperData();
        $entity = $event->getApplicationTripLocationPath();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationTripLocationPathUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationTripLocationPathUpdateEvent $event): void
    {
        $entity = $event->getApplicationTripLocationPath();
        $isFlush = $event->isFlush();
        $this->applicationTripLocationPathRepository->saveApplicationTripLocationPath($entity, $isFlush);
    }
}