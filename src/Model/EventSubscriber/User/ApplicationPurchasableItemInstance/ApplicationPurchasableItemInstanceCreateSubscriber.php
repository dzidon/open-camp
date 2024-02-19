<?php

namespace App\Model\EventSubscriber\User\ApplicationPurchasableItemInstance;

use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\User\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceCreateEvent;
use App\Model\Repository\ApplicationPurchasableItemInstanceRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemInstanceCreateSubscriber
{
    private ApplicationPurchasableItemInstanceRepositoryInterface $applicationPurchasableItemInstanceRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationPurchasableItemInstanceRepositoryInterface $applicationPurchasableItemInstanceRepository,
                                DataTransferRegistryInterface                         $dataTransfer)
    {
        $this->applicationPurchasableItemInstanceRepository = $applicationPurchasableItemInstanceRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationPurchasableItemInstanceCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationPurchasableItemInstanceCreateEvent $event): void
    {
        $data = $event->getApplicationPurchasableItemInstanceData();
        $applicationCamper = $event->getApplicationCamper();
        $chosenVariantValues = [];

        foreach ($data->getApplicationPurchasableItemVariantsData() as $applicationPurchasableItemVariantDatum)
        {
            $variant = $applicationPurchasableItemVariantDatum->getLabel();
            $value = $applicationPurchasableItemVariantDatum->getValue();
            $chosenVariantValues[$variant] = $value;
        }

        $applicationPurchasableItem = $event->getApplicationPurchasableItem();
        $amount = $data->getAmount();
        $applicationPurchasableItemInstance = new ApplicationPurchasableItemInstance(
            $chosenVariantValues,
            $amount,
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
        $this->applicationPurchasableItemInstanceRepository->saveApplicationPurchasableItemInstance($applicationPurchasableItemInstance, $isFlush);
    }
}