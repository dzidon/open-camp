<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Model\Entity\PurchasableItemVariant;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link PurchasableItemVariantData} to {@link PurchasableItemVariant} and vice versa.
 */
class PurchasableItemVariantDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof PurchasableItemVariantData && $entity instanceof PurchasableItemVariant;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var PurchasableItemVariantData $purchasableItemVariantData */
        /** @var PurchasableItemVariant $purchasableItemVariant */
        $purchasableItemVariantData = $data;
        $purchasableItemVariant = $entity;

        $purchasableItemVariantData->setName($purchasableItemVariant->getName());
        $purchasableItemVariantData->setPriority($purchasableItemVariant->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var PurchasableItemVariantData $purchasableItemVariantData */
        /** @var PurchasableItemVariant $purchasableItemVariant */
        $purchasableItemVariantData = $data;
        $purchasableItemVariant = $entity;

        $purchasableItemVariant->setName($purchasableItemVariantData->getName());
        $purchasableItemVariant->setPriority($purchasableItemVariantData->getPriority());
    }
}