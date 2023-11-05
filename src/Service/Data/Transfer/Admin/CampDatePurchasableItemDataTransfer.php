<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\CampDatePurchasableItem;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampDatePurchasableItemData} to {@link CampDatePurchasableItem} and vice versa.
 */
class CampDatePurchasableItemDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampDatePurchasableItemData && $entity instanceof CampDatePurchasableItem;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampDatePurchasableItemData $campDatePurchasableItemData */
        /** @var CampDatePurchasableItem $campDatePurchasableItem */
        $campDatePurchasableItemData = $data;
        $campDatePurchasableItem = $entity;

        $campDatePurchasableItemData->setPurchasableItem($campDatePurchasableItem->getPurchasableItem());
        $campDatePurchasableItemData->setPriority($campDatePurchasableItem->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampDatePurchasableItemData $campDatePurchasableItemData */
        /** @var CampDatePurchasableItem $campDatePurchasableItem */
        $campDatePurchasableItemData = $data;
        $campDatePurchasableItem = $entity;

        $campDatePurchasableItem->setPurchasableItem($campDatePurchasableItemData->getPurchasableItem());
        $campDatePurchasableItem->setPriority($campDatePurchasableItemData->getPriority());
    }
}