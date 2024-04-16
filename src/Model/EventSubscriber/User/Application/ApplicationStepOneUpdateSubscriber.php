<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDeleteEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationStepOneUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCamperRepositoryInterface $applicationCamperRepository;

    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationRepositoryInterface       $applicationRepository,
                                ApplicationCamperRepositoryInterface $applicationCamperRepository,
                                DataTransferRegistryInterface        $dataTransfer,
                                EventDispatcherInterface             $eventDispatcher)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCamperRepository = $applicationCamperRepository;
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 700)]
    public function onUpdateFillEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 600)]
    public function onUpdateFixOverflownPurchasableItems(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $overflownPurchasableItems = $application->getOverflownPurchasableItems();

        foreach ($overflownPurchasableItems as $overflownPurchasableItem)
        {
            foreach ($overflownPurchasableItem->getApplicationPurchasableItemInstances() as $instance)
            {
                $instanceDeleteEvent = new ApplicationPurchasableItemInstanceDeleteEvent($instance);
                $instanceDeleteEvent->setIsFlush(false);
                $this->eventDispatcher->dispatch($instanceDeleteEvent, $instanceDeleteEvent::NAME);
            }
        }
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 500)]
    public function onUpdateSetApplicationCampersTripsInThePast(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationCampers = $application->getApplicationCampers();

        foreach ($applicationCampers as $applicationCamper)
        {
            $numberOfOtherCompleteAcceptedApplications = $this->applicationCamperRepository
                ->getNumberOfOtherCompleteAcceptedApplications($applicationCamper)
            ;

            $applicationCamper->setTripsInThePast($numberOfOtherCompleteAcceptedApplications);
        }
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 400)]
    public function onUpdateResetSiblingsDiscountIfInvalid(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $application->resetSiblingsDiscountIfIntervalNotEligibleForNumberOfCampers();
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 300)]
    public function onUpdateResetPaymentMethodIfInvalid(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $paymentMethod = $application->getPaymentMethod();

        if ($paymentMethod !== null && $paymentMethod->isForBusinessesOnly() && !$application->isBuyerBusiness())
        {
            $application->setPaymentMethod(null);
        }
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 200)]
    public function onUpdateCacheAllFullPrices(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $application->cacheAllFullPrices();
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}