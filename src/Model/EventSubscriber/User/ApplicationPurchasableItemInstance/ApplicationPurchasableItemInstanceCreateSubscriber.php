<?php

namespace App\Model\EventSubscriber\User\ApplicationPurchasableItemInstance;

use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceCreateEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceCreateSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $repository,
                                DataTransferRegistryInterface                         $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationPurchasableItemInstanceCreateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemInstanceData();
        $priority = $event->getPriority();
        $applicationCamper = $event->getApplicationCamper();
        $chosenVariantValues = $data->getChosenApplicationPurchasableItemVariants();
        $applicationPurchasableItem = $event->getApplicationPurchasableItem();
        $amount = $data->getAmount();

        $applicationPurchasableItemInstance = new ApplicationPurchasableItemInstance(
            $chosenVariantValues,
            $amount,
            $priority,
            $applicationPurchasableItem,
            $applicationCamper
        );

        $this->dataTransfer->fillEntity($data, $applicationPurchasableItemInstance);
        $event->setApplicationPurchasableItemInstance($applicationPurchasableItemInstance);
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationPurchasableItemInstanceCreateEvent $event): void
    {
        $applicationPurchasableItemInstance = $event->getApplicationPurchasableItemInstance();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationPurchasableItemInstance($applicationPurchasableItemInstance, $isFlush);
    }
}