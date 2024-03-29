<?php

namespace App\Model\EventSubscriber\Admin\ApplicationCamper;

use App\Model\Entity\ApplicationCamper;
use App\Model\Event\Admin\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationCamperCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private ApplicationCamperRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, ApplicationCamperRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 600)]
    public function onCreateFillEntity(ApplicationCamperCreateEvent $event): void
    {
        $applicationCamperData = $event->getApplicationCamperData();
        $camperData = $applicationCamperData->getCamperData();
        $application = $event->getApplication();
        $priority = $application->getLowestApplicationCamperPriority() - 1;
        $entity = new ApplicationCamper(
            $camperData->getNameFirst(),
            $camperData->getNameLast(),
            $camperData->getGender(),
            $priority,
            $camperData->getBornAt(),
            $application
        );

        $this->dataTransfer->fillEntity($applicationCamperData, $entity);
        $event->setApplicationCamper($entity);
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 500)]
    public function onCreateSetApplicationCampersTripsInThePast(ApplicationCamperCreateEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $numberOfOtherCompleteAcceptedApplications = $this->repository->getNumberOfOtherCompleteAcceptedApplications($applicationCamper);
        $applicationCamper->setTripsInThePast($numberOfOtherCompleteAcceptedApplications);
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationCamperCreateEvent $event): void
    {
        $entity = $event->getApplicationCamper();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationCamper($entity, $isFlush);
    }
}