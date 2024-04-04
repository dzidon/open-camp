<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneCreateEvent;
use App\Model\Event\User\ApplicationPurchasableItem\ApplicationPurchasableItemCreateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationStepOneCreateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCamperRepositoryInterface $applicationCamperRepository;

    private ApplicationFactoryInterface $applicationFactory;

    private EventDispatcherInterface $eventDispatcher;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface       $applicationRepository,
                                ApplicationCamperRepositoryInterface $applicationCamperRepository,
                                ApplicationFactoryInterface          $applicationFactory,
                                EventDispatcherInterface             $eventDispatcher,
                                DataTransferRegistryInterface        $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCamperRepository = $applicationCamperRepository;
        $this->applicationFactory = $applicationFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 500)]
    public function onCreateInstantiateEntity(ApplicationStepOneCreateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $campDate = $event->getCampDate();
        $user = $event->getUser();
        $application = $this->applicationFactory->createApplication($data, $campDate, $user);
        $this->dataTransfer->fillEntity($data, $application);
        $event->setApplication($application);
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 400)]
    public function onCreateInstantiatePurchasableItems(ApplicationStepOneCreateEvent $event): void
    {
        $campDate = $event->getCampDate();
        $application = $event->getApplication();

        foreach ($campDate->getCampDatePurchasableItems() as $campDatePurchasableItem)
        {
            $event = new ApplicationPurchasableItemCreateEvent($campDatePurchasableItem, $application);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 300)]
    public function onCreateSetApplicationCampersTripsInThePast(ApplicationStepOneCreateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationCampers = $application->getApplicationCampers();

        foreach ($applicationCampers as $applicationCamper)
        {
            $numberOfOtherCompleteAcceptedApplications = $this->applicationCamperRepository->getNumberOfOtherCompleteAcceptedApplications($applicationCamper);
            $applicationCamper->setTripsInThePast($numberOfOtherCompleteAcceptedApplications);
        }
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 200)]
    public function onCreateCacheAllFullPrices(ApplicationStepOneCreateEvent $event): void
    {
        $application = $event->getApplication();
        $application->cacheAllFullPrices();
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationStepOneCreateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}