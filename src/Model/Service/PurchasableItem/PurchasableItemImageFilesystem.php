<?php

namespace App\Model\Service\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class PurchasableItemImageFilesystem implements PurchasableItemImageFilesystemInterface
{
    private FilesystemOperator $purchasableItemImageStorage;

    private string $purchasableItemImagePublicPathPrefix;

    private string $purchasableItemImageDirectory;

    private string $noImagePath;

    public function __construct(FilesystemOperator $purchasableItemImageStorage,
                                string             $purchasableItemImagePublicPathPrefix,
                                string             $purchasableItemImageDirectory,
                                string             $noImagePath)
    {
        $this->purchasableItemImageStorage = $purchasableItemImageStorage;

        $this->purchasableItemImagePublicPathPrefix = $purchasableItemImagePublicPathPrefix;
        $this->purchasableItemImageDirectory = $purchasableItemImageDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageLastModified(PurchasableItem $purchasableItem): ?int
    {
        if ($purchasableItem->getImageExtension() === null)
        {
            return null;
        }

        $fileName = $this->getPurchasableItemImageName($purchasableItem);

        if (!$this->purchasableItemImageStorage->has($fileName))
        {
            return null;
        }

        return $this->purchasableItemImageStorage->lastModified($fileName);
    }

    /**
     * @inheritDoc
     */
    public function isUrlPlaceholder(string $publicUrl): bool
    {
        return explode('?', $publicUrl)[0] === $this->getNoImageUrl();
    }

    /**
     * @inheritDoc
     */
    public function getImagePublicUrl(PurchasableItem $purchasableItem): string
    {
        $noImageUrl = $this->getNoImageUrl();

        if ($purchasableItem->getImageExtension() === null)
        {
            return $noImageUrl;
        }

        $fileName = $this->getPurchasableItemImageName($purchasableItem);

        if (!$this->purchasableItemImageStorage->has($fileName))
        {
            return $noImageUrl;
        }

        $fileName = $this->getPurchasableItemImageName($purchasableItem);

        return $this->purchasableItemImagePublicPathPrefix . $this->purchasableItemImageDirectory . '/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public function uploadImageFile(File $file, PurchasableItem $purchasableItem): void
    {
        $this->removeImageFile($purchasableItem);

        $extension = $file->guessExtension();
        $purchasableItem->setImageExtension($extension);
        $idString = $purchasableItem
            ->getId()
            ->toRfc4122()
        ;

        $newFileName = $idString . '.' . $extension;
        $contents = $file->getContent();
        $this->purchasableItemImageStorage->write($newFileName, $contents);
    }

    /**
     * @inheritDoc
     */
    public function removeImageFile(PurchasableItem $purchasableItem): void
    {
        if ($purchasableItem->getImageExtension() === null)
        {
            return;
        }

        $fileName = $this->getPurchasableItemImageName($purchasableItem);
        $this->purchasableItemImageStorage->delete($fileName);
        $purchasableItem->setImageExtension(null);
    }

    private function getPurchasableItemImageName(PurchasableItem $purchasableItem): string
    {
        $purchasableItemImageId = $purchasableItem->getId();

        return $purchasableItemImageId->toRfc4122() . '.' . $purchasableItem->getImageExtension();
    }

    private function getNoImageUrl(): string
    {
        return $this->purchasableItemImagePublicPathPrefix . $this->noImagePath;
    }
}