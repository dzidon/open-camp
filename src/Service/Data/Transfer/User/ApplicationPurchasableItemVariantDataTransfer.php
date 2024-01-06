<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationPurchasableItemVariantData;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationPurchasableItemVariantData} to {@link ApplicationPurchasableItemVariantData} and vice versa.
 */
class ApplicationPurchasableItemVariantDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationPurchasableItemVariantData && $entity instanceof ApplicationPurchasableItemInstance;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemVariantData $applicationPurchasableItemVariantData */
        /** @var ApplicationPurchasableItemInstance $applicationPurchasableItemInstance */
        $applicationPurchasableItemVariantData = $data;
        $applicationPurchasableItemInstance = $entity;

        $variant = $applicationPurchasableItemVariantData->getLabel();
        $value = $applicationPurchasableItemInstance->getChosenVariantValue($variant);
        $applicationPurchasableItemVariantData->setValue($value);
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationPurchasableItemVariantData $applicationPurchasableItemVariantData */
        /** @var ApplicationPurchasableItemInstance $applicationPurchasableItemInstance */
        $applicationPurchasableItemVariantData = $data;
        $applicationPurchasableItemInstance = $entity;

        $variant = $applicationPurchasableItemVariantData->getLabel();
        $value = $applicationPurchasableItemVariantData->getValue();
        $applicationPurchasableItemInstance->setChosenVariantValue($variant, $value);
    }
}