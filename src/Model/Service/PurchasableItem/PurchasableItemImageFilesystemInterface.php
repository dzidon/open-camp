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
     * Gets the timestamp of last modification.
     *
     * @param PurchasableItem $purchasableItem
     * @return int|null
     */
    public function getImageLastModified(PurchasableItem $purchasableItem): ?int;

    /**
     * Returns true if the given URL is equal to the purchasable item image placeholder.
     *
     * @param string $publicUrl
     * @return bool
     */
    public function isUrlPlaceholder(string $publicUrl): bool;

    /**
     * Gets the public path to the given purchasable item image.
     *
     * @param PurchasableItem $purchasableItem
     * @return string
     */
    public function getImagePublicUrl(PurchasableItem $purchasableItem): string;

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