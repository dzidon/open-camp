<?php

namespace App\Model\Module\CampCatalog\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class PurchasableItemImageFilesystem implements PurchasableItemImageFilesystemInterface
{
    private string $purchasableItemImageDirectory;
    private string $noImagePath;
    private string $kernelProjectDirectory;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem,
                                string     $purchasableItemImageDirectory,
                                string     $noImagePath,
                                string     $kernelProjectDirectory)
    {
        $this->filesystem = $filesystem;

        $this->purchasableItemImageDirectory = $purchasableItemImageDirectory;
        $this->kernelProjectDirectory = $kernelProjectDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageFilePath(PurchasableItem $purchasableItem): string
    {
        if ($purchasableItem->getImageExtension() === null)
        {
            return $this->noImagePath;
        }

        $id = $purchasableItem->getId();

        return $this->purchasableItemImageDirectory . '/' . $id->toRfc4122() . '.' . $purchasableItem->getImageExtension();
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
        $file->move($this->purchasableItemImageDirectory, $newFileName);
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

        $filePath = $this->kernelProjectDirectory . '/public/' . $this->getImageFilePath($purchasableItem);
        $this->filesystem->remove($filePath);
        $purchasableItem->setImageExtension(null);
    }
}