<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Module\CampCatalog\PurchasableItem\PurchasableItemImageFilesystemInterface;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link PurchasableItemData} to {@link PurchasableItem} and vice versa.
 */
class PurchasableItemDataTransfer implements DataTransferInterface
{
    private PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem;

    public function __construct(PurchasableItemImageFilesystemInterface $purchasableItemImageFilesystem)
    {
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof PurchasableItemData && $entity instanceof PurchasableItem;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var PurchasableItemData $purchasableItemData */
        /** @var PurchasableItem $purchasableItem */
        $purchasableItemData = $data;
        $purchasableItem = $entity;

        $purchasableItemData->setName($purchasableItem->getName());
        $purchasableItemData->setLabel($purchasableItem->getLabel());
        $purchasableItemData->setPrice($purchasableItem->getPrice());
        $purchasableItemData->setMaxAmount($purchasableItem->getMaxAmount());
        $purchasableItemData->setIsGlobal($purchasableItem->isGlobal());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var PurchasableItemData $purchasableItemData */
        /** @var PurchasableItem $purchasableItem */
        $purchasableItemData = $data;
        $purchasableItem = $entity;

        $purchasableItem->setName($purchasableItemData->getName());
        $purchasableItem->setLabel($purchasableItemData->getLabel());
        $purchasableItem->setPrice($purchasableItemData->getPrice());
        $purchasableItem->setMaxAmount($purchasableItemData->getMaxAmount());
        $purchasableItem->setIsGlobal($purchasableItemData->isGlobal());

        $image = $purchasableItemData->getImage();
        $removeImage = $purchasableItemData->removeImage();

        if ($image !== null && !$removeImage)
        {
            $this->purchasableItemImageFilesystem->uploadImageFile($image, $purchasableItem);
        }

        if ($removeImage)
        {
            $this->purchasableItemImageFilesystem->removeImageFile($purchasableItem);
        }
    }
}