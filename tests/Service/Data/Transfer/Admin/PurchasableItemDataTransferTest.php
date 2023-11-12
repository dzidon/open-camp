<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Module\CampCatalog\PurchasableItem\PurchasableItemImageFilesystem;
use App\Service\Data\Transfer\Admin\PurchasableItemDataTransfer;
use App\Tests\Library\Http\File\FileMock;
use App\Tests\Service\Filesystem\FilesystemMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();

        $expectedName = 'Name';
        $expectedLabel = 'Label';
        $expectedPrice = 1000.0;
        $maxAmount = 10;
        $purchasableItem = new PurchasableItem($expectedName, $expectedLabel, $expectedPrice, $maxAmount);
        $purchasableItem->setIsGlobal(true);

        $data = new PurchasableItemData();
        $dataTransfer->fillData($data, $purchasableItem);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedLabel, $data->getLabel());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($maxAmount, $data->getMaxAmount());
        $this->assertTrue($data->isGlobal());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();

        $expectedName = 'Name';
        $expectedLabel = 'Label';
        $expectedPrice = 1000.0;
        $maxAmount = 10;
        $purchasableItem = new PurchasableItem('', '', 0.0, 0);

        $data = new PurchasableItemData();
        $data->setName($expectedName);
        $data->setLabel($expectedLabel);
        $data->setPrice($expectedPrice);
        $data->setMaxAmount($maxAmount);
        $data->setIsGlobal(true);
        $dataTransfer->fillEntity($data, $purchasableItem);

        $this->assertSame($expectedName, $purchasableItem->getName());
        $this->assertSame($expectedLabel, $purchasableItem->getLabel());
        $this->assertSame($expectedPrice, $purchasableItem->getPrice());
        $this->assertSame($maxAmount, $purchasableItem->getMaxAmount());
        $this->assertTrue($purchasableItem->isGlobal());
    }

    public function testFillEntityRemoveImage(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();
        $filesystemMock = $this->getFilesystemMock();

        $newImage = new FileMock('jpg');
        $data = new PurchasableItemData();
        $data->setName("Name");
        $data->setLabel("Label");
        $data->setPrice(1000.0);
        $data->setMaxAmount(10);
        $data->setImage($newImage);
        $data->setRemoveImage(true);

        $purchasableItem = new PurchasableItem('', '', 0.0, 0);
        $purchasableItem->setImageExtension('png');
        $purchasableItemIdString = $purchasableItem->getId()->toRfc4122();

        $dataTransfer->fillEntity($data, $purchasableItem);

        $removalPath = 'kernel-dir/public/img/dynamic/purchasable-item/' . $purchasableItemIdString . '.png';
        $this->assertContains($removalPath, $filesystemMock->getRemovedFiles());
        $this->assertNull($newImage->getMovedDirectory());
        $this->assertNull($newImage->getMovedName());
    }

    public function testFillEntityUploadImage(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();
        $filesystemMock = $this->getFilesystemMock();

        $newImage = new FileMock('jpg');
        $data = new PurchasableItemData();
        $data->setName("Name");
        $data->setLabel("Label");
        $data->setPrice(1000.0);
        $data->setMaxAmount(10);
        $data->setImage($newImage);

        $purchasableItem = new PurchasableItem('', '', 0.0, 0);
        $purchasableItem->setImageExtension('png');
        $purchasableItemIdString = $purchasableItem->getId()->toRfc4122();

        $dataTransfer->fillEntity($data, $purchasableItem);

        $removalPath = 'kernel-dir/public/img/dynamic/purchasable-item/' . $purchasableItemIdString . '.png';
        $this->assertContains($removalPath, $filesystemMock->getRemovedFiles());
        $this->assertSame('img/dynamic/purchasable-item', $newImage->getMovedDirectory());
        $this->assertSame($purchasableItemIdString . '.jpg', $newImage->getMovedName());
    }

    private function getFilesystemMock(): FilesystemMock
    {
        $container = static::getContainer();

        /** @var FilesystemMock $filesystemMock */
        $filesystemMock = $container->get(FilesystemMock::class);

        return $filesystemMock;
    }

    private function getPurchasableItemDataTransfer(): PurchasableItemDataTransfer
    {
        $container = static::getContainer();

        /** @var PurchasableItemDataTransfer $dataTransfer */
        $dataTransfer = $container->get(PurchasableItemDataTransfer::class);

        return $dataTransfer;
    }

    protected function setUp(): void
    {
        $container = static::getContainer();
        $imageDirectory = $container->getParameter('app.purchasable_item_image_directory');

        $filesystemMock = new FilesystemMock();
        $purchasableItemImageFilesystem = new PurchasableItemImageFilesystem($filesystemMock, $imageDirectory, 'no-image.jpg', 'kernel-dir');

        $container->set(FilesystemMock::class, $filesystemMock);
        $container->set(PurchasableItemImageFilesystem::class, $purchasableItemImageFilesystem);
    }
}