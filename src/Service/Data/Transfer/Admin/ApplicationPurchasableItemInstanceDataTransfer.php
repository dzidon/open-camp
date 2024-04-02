<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
use App\Library\Data\Admin\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\Admin\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceVariantUpdateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Transfers data from {@link ApplicationPurchasableItemInstanceData} to {@link ApplicationPurchasableItemInstance} and vice versa.
 */
class ApplicationPurchasableItemInstanceDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationPurchasableItemInstanceData && $entity instanceof ApplicationPurchasableItemInstance;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemInstanceData $applicationPurchasableItemInstanceData */
        /** @var ApplicationPurchasableItemInstance $applicationPurchasableItemInstance */
        $applicationPurchasableItemInstanceData = $data;
        $applicationPurchasableItemInstance = $entity;

        $applicationPurchasableItemInstanceData->setAmount($applicationPurchasableItemInstance->getAmount());

        foreach ($applicationPurchasableItemInstance->getChosenVariantValues() as $variant => $value)
        {
            $validVariantValues = $applicationPurchasableItemInstance
                ->getApplicationPurchasableItem()
                ->getValidVariantValues($variant)
            ;

            $applicationPurchasableItemVariantData = new ApplicationPurchasableItemVariantData($variant, $validVariantValues);
            $this->dataTransfer->fillData($applicationPurchasableItemVariantData, $applicationPurchasableItemInstance);
            $applicationPurchasableItemInstanceData->addApplicationPurchasableItemVariantsDatum($applicationPurchasableItemVariantData);
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemInstanceData $applicationPurchasableItemInstanceData */
        /** @var ApplicationPurchasableItemInstance $applicationPurchasableItemInstance */
        $applicationPurchasableItemInstanceData = $data;
        $applicationPurchasableItemInstance = $entity;

        $applicationPurchasableItemInstance->setAmount($applicationPurchasableItemInstanceData->getAmount());

        foreach ($applicationPurchasableItemInstanceData->getApplicationPurchasableItemVariantsData() as $applicationPurchasableItemVariantData)
        {
            $event = new ApplicationPurchasableItemInstanceVariantUpdateEvent(
                $applicationPurchasableItemVariantData,
                $applicationPurchasableItemInstance
            );

            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }
}