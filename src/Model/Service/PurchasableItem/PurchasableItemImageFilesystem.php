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
        $imageFileName = $purchasableItem->getImageFileName();

        if ($imageFileName === null || !$this->purchasableItemImageStorage->has($imageFileName))
        {
            return null;
        }

        return $this->purchasableItemImageStorage->lastModified($imageFileName);
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
        $imageFileName = $purchasableItem->getImageFileName();

        if ($imageFileName === null || !$this->purchasableItemImageStorage->has($imageFileName))
        {
            return $noImageUrl;
        }

        return $this->purchasableItemImagePublicPathPrefix . $this->purchasableItemImageDirectory . '/' . $imageFileName;
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
        $imageFileName = $purchasableItem->getImageFileName();

        if ($imageFileName === null)
        {
            return;
        }

        $this->purchasableItemImageStorage->delete($imageFileName);
        $purchasableItem->setImageExtension(null);
    }

    private function getNoImageUrl(): string
    {
        return $this->purchasableItemImagePublicPathPrefix . $this->noImagePath;
    }
}