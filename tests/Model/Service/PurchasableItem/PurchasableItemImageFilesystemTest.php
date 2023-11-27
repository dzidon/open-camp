<?php

namespace App\Tests\Model\Service\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Service\PurchasableItem\PurchasableItemImageFilesystem;
use App\Tests\Library\Http\File\FileMock;
use App\Tests\Service\Filesystem\FilesystemMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemImageFilesystemTest extends KernelTestCase
{
    private FilesystemMock $filesystemMock;
    private PurchasableItemImageFilesystem $purchasableItemImageFilesystem;
    private PurchasableItem $purchasableItem;
    private FileMock $newImage;
    private string $purchasableItemIdString;

    public function testUploadImageFile(): void
    {
        $this->purchasableItemImageFilesystem->uploadImageFile($this->newImage, $this->purchasableItem);

        $this->assertContains('kernel-dir/public/img/dynamic/purchasable-item/' . $this->purchasableItemIdString . '.png', $this->filesystemMock->getRemovedFiles());
        $this->assertSame('img/dynamic/purchasable-item', $this->newImage->getMovedDirectory());
        $this->assertSame($this->purchasableItemIdString . '.jpg', $this->newImage->getMovedName());
    }

    public function testUploadImageFileIfNull(): void
    {
        $this->purchasableItem->setImageExtension(null);
        $this->purchasableItemImageFilesystem->uploadImageFile($this->newImage, $this->purchasableItem);

        $this->assertEmpty($this->filesystemMock->getRemovedFiles());
        $this->assertSame('img/dynamic/purchasable-item', $this->newImage->getMovedDirectory());
        $this->assertSame($this->purchasableItemIdString . '.jpg', $this->newImage->getMovedName());
    }

    public function testGetImageFilePath(): void
    {
        $path = $this->purchasableItemImageFilesystem->getImageFilePath($this->purchasableItem);

        $this->assertSame('img/dynamic/purchasable-item/' . $this->purchasableItemIdString . '.png', $path);
    }

    public function testGetImageFilePathIfNull(): void
    {
        $this->purchasableItem->setImageExtension(null);
        $path = $this->purchasableItemImageFilesystem->getImageFilePath($this->purchasableItem);

        $this->assertSame('no-image.jpg', $path);
    }

    public function testRemoveFile(): void
    {
        $this->purchasableItemImageFilesystem->removeImageFile($this->purchasableItem);

        $this->assertContains('kernel-dir/public/img/dynamic/purchasable-item/' . $this->purchasableItemIdString . '.png', $this->filesystemMock->getRemovedFiles());
    }

    public function testRemoveFileIfNull(): void
    {
        $this->purchasableItem->setImageExtension(null);
        $this->purchasableItemImageFilesystem->removeImageFile($this->purchasableItem);

        $this->assertEmpty($this->filesystemMock->getRemovedFiles());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();
        $imageDirectory = $container->getParameter('app.purchasable_item_image_directory');

        $this->newImage = new FileMock('jpg');

        $this->filesystemMock = new FilesystemMock();
        $this->purchasableItemImageFilesystem = new PurchasableItemImageFilesystem($this->filesystemMock, $imageDirectory, 'no-image.jpg', 'kernel-dir');
        $this->purchasableItem = new PurchasableItem("Item", 'Item...', 1000.0, 10);
        $this->purchasableItem->setImageExtension('png');
        $this->purchasableItemIdString = $this->purchasableItem
            ->getId()
            ->toRfc4122()
        ;
    }
}