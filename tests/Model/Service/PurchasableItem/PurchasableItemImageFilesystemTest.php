<?php

namespace App\Tests\Model\Service\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Service\PurchasableItem\PurchasableItemImageFilesystem;
use App\Tests\Library\Http\File\FileMock;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemImageFilesystemTest extends KernelTestCase
{
    private FilesystemOperator $storage;

    private PurchasableItemImageFilesystem $purchasableItemImageFilesystem;

    private PurchasableItem $purchasableItem;

    private FileMock $imageFile;

    private string $purchasableItemIdString;

    public function testGetImageLastModified(): void
    {
        $this->purchasableItemImageFilesystem->uploadImageFile($this->imageFile, $this->purchasableItem);
        $this->assertSame(time(), $this->purchasableItemImageFilesystem->getImageLastModified($this->purchasableItem));
    }

    public function testGetImageLastModifiedForNonexistentFile(): void
    {
        $this->assertNull($this->purchasableItemImageFilesystem->getImageLastModified($this->purchasableItem));
    }

    public function testIsUrlPlaceholder(): void
    {
        $this->assertTrue($this->purchasableItemImageFilesystem->isUrlPlaceholder('/files/static/placeholder.jpg'));
        $this->assertTrue($this->purchasableItemImageFilesystem->isUrlPlaceholder('/files/static/placeholder.jpg?foo=bar&xyz=123'));
        $this->assertFalse($this->purchasableItemImageFilesystem->isUrlPlaceholder('/a/b/c'));
    }

    public function testGetImagePublicUrl(): void
    {
        $this->purchasableItemImageFilesystem->uploadImageFile($this->imageFile, $this->purchasableItem);
        $actualUrl = $this->purchasableItemImageFilesystem->getImagePublicUrl($this->purchasableItem);
        $expectedUrl = '/files/dynamic/purchasable-item/' . $this->purchasableItemIdString . '.png';

        $this->assertSame($expectedUrl, $actualUrl);
    }

    public function testGetImagePublicUrlWithNonexistentFile(): void
    {
        $this->purchasableItem->setImageExtension('jpg');
        $actualUrl = $this->purchasableItemImageFilesystem->getImagePublicUrl($this->purchasableItem);

        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);
    }

    public function testGetImagePublicUrlWithNullCampImage(): void
    {
        $this->purchasableItem->setImageExtension(null);
        $actualUrl = $this->purchasableItemImageFilesystem->getImagePublicUrl($this->purchasableItem);

        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);
    }

    public function testUploadAndRemoveFile(): void
    {
        $fileName = $this->purchasableItemIdString . '.png';

        $this->purchasableItemImageFilesystem->uploadImageFile($this->imageFile, $this->purchasableItem);
        $this->assertTrue($this->storage->has($fileName));
        $this->assertSame('png', $this->purchasableItem->getImageExtension());

        $this->purchasableItemImageFilesystem->removeImageFile($this->purchasableItem);
        $this->assertFalse($this->storage->has($fileName));
        $this->assertNull($this->purchasableItem->getImageExtension());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var PurchasableItemImageFilesystem $purchasableItemImageFilesystem */
        $purchasableItemImageFilesystem = $container->get(PurchasableItemImageFilesystem::class);
        $this->purchasableItemImageFilesystem = $purchasableItemImageFilesystem;

        /** @var FilesystemOperator $storage */
        $storage = $container->get('purchasable_item_image.storage');
        $this->storage = $storage;

        $this->purchasableItem = new PurchasableItem('Item', 'Item', 100.0, 10);
        $this->purchasableItemIdString = $this->purchasableItem
            ->getId()
            ->toRfc4122()
        ;

        $this->imageFile = new FileMock('png', 'image.png', 'Content...');
    }
}