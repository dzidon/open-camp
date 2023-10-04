<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link PurchasableItemVariantValueData} to {@link PurchasableItemVariantValue} and vice versa.
 */
class PurchasableItemVariantValueDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof PurchasableItemVariantValueData && $entity instanceof PurchasableItemVariantValue;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var PurchasableItemVariantValueData $purchasableItemVariantValueData */
        /** @var PurchasableItemVariantValue $purchasableItemVariantValue */
        $purchasableItemVariantValueData = $data;
        $purchasableItemVariantValue = $entity;

        $purchasableItemVariantValueData->setName($purchasableItemVariantValue->getName());
        $purchasableItemVariantValueData->setPriority($purchasableItemVariantValue->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var PurchasableItemVariantValueData $purchasableItemVariantValueData */
        /** @var PurchasableItemVariantValue $purchasableItemVariantValue */
        $purchasableItemVariantValueData = $data;
        $purchasableItemVariantValue = $entity;

        $purchasableItemVariantValue->setName($purchasableItemVariantValueData->getName());
        $purchasableItemVariantValue->setPriority($purchasableItemVariantValueData->getPriority());
    }
}