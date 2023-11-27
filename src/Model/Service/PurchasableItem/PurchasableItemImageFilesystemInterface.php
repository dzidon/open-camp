<?php

namespace App\Model\Service\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Helper service for purchasable item image files.
 */
interface PurchasableItemImageFilesystemInterface
{
    /**
     * Gets the path to the given purchasable item image.
     *
     * @param PurchasableItem $purchasableItem
     * @return string
     */
    public function getImageFilePath(PurchasableItem $purchasableItem): string;

    /**
     * Uploads the given file and attaches it to the given purchasable item.
     *
     * @param File $file
     * @param PurchasableItem $purchasableItem
     * @return void
     */
    public function uploadImageFile(File $file, PurchasableItem $purchasableItem): void;

    /**
     * Removes the image file of the given purchasable item.
     *
     * @param PurchasableItem $purchasableItem
     * @return void
     */
    public function removeImageFile(PurchasableItem $purchasableItem): void;
}