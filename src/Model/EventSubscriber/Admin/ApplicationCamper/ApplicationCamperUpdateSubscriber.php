<?php

namespace App\Model\EventSubscriber\Admin\ApplicationCamper;

use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationCamperUpdateSubscriber
{
    private ApplicationCamperRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationCamperRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 600)]
    public function onUpdateFillEntity(ApplicationCamperUpdateEvent $event): void
    {
        $data = $event->getApplicationCamperData();
        $entity = $event->getApplicationCamper();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 500)]
    public function onCreateSetApplicationCampersTripsInThePast(ApplicationCamperUpdateEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $numberOfOtherCompleteAcceptedApplications = $this->repository->getNumberOfOtherCompleteAcceptedApplications($applicationCamper);
        $applicationCamper->setTripsInThePast($numberOfOtherCompleteAcceptedApplications);
    }

    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationCamperUpdateEvent $event): void
    {
        $entity = $event->getApplicationCamper();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationCamper($entity, $isFlush);
    }
}