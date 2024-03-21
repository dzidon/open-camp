<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationPurchasableItemInstanceData} to {@link ApplicationPurchasableItemInstance} and vice versa.
 */
class ApplicationPurchasableItemInstanceDataTransfer implements DataTransferInterface
{
    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(DataTransferRegistryInterface $dataTransfer)
    {
        $this->dataTransfer = $dataTransfer;
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
            $this->dataTransfer->fillEntity($applicationPurchasableItemVariantData, $applicationPurchasableItemInstance);
        }
    }
}