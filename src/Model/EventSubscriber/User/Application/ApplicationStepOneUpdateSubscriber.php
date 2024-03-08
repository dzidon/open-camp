<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationOverflownPurchasableItemsRemoverInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationStepOneUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCamperRepositoryInterface $applicationCamperRepository;

    private ApplicationOverflownPurchasableItemsRemoverInterface $applicationOverflownPurchasableItemsRemover;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface                       $applicationRepository,
                                ApplicationCamperRepositoryInterface                 $applicationCamperRepository,
                                ApplicationOverflownPurchasableItemsRemoverInterface $applicationOverflownPurchasableItemsRemover,
                                DataTransferRegistryInterface                        $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCamperRepository = $applicationCamperRepository;
        $this->applicationOverflownPurchasableItemsRemover = $applicationOverflownPurchasableItemsRemover;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 500)]
    public function onUpdateFillEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 400)]
    public function onUpdateFixOverflownPurchasableItems(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationOverflownPurchasableItemsRemover->removeOverflownPurchasableItems($application);
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 300)]
    public function onUpdateSetApplicationCampersTripsInThePast(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationCampers = $application->getApplicationCampers();

        foreach ($applicationCampers as $applicationCamper)
        {
            $numberOfOtherCompleteAcceptedApplications = $this->applicationCamperRepository->getNumberOfOtherCompleteAcceptedApplications($applicationCamper);
            $applicationCamper->setTripsInThePast($numberOfOtherCompleteAcceptedApplications);
        }
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 200)]
    public function onUpdateResetSiblingsDiscountIfInvalid(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $application->resetSiblingsDiscountIfIntervalNotEligibleForNumberOfCampers();
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}