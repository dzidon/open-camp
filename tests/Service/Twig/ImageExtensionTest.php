<?php

namespace App\Tests\Service\Twig;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\User;
use App\Service\Twig\ImageExtension;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageExtensionTest extends KernelTestCase
{
    private ImageExtension $extension;

    private FilesystemOperator $campImageStorage;

    private FilesystemOperator $purchasableItemImageStorage;

    private FilesystemOperator $userImageStorage;

    public function testGetCompanyLogoUrlWithPrefix(): void
    {
        $urlWithPrefix = $this->extension->getCompanyLogoUrl();
        $this->assertSame('/files/static/company_logo.png', $urlWithPrefix);
    }

    public function testGetCompanyLogoUrlWithoutPrefix(): void
    {
        $urlWithPrefix = $this->extension->getCompanyLogoUrl(false);
        $this->assertSame('files/static/company_logo.png', $urlWithPrefix);
    }

    public function testGetCampImageUrl(): void
    {
        // null file

        $actualUrl = $this->extension->getCampImageUrl(null);
        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);

        // existing file

        $camp = new Camp('Camp', 'camp', 5, 10, 321);
        $campImage = new CampImage(100, 'png', $camp);
        $campImageIdString = $campImage
            ->getId()
            ->toRfc4122()
        ;

        $campImageFileName = $campImageIdString . '.png';

        $this->campImageStorage->write($campImageFileName, 'Contents...');
        $lastModified = $this->campImageStorage->lastModified($campImageFileName);

        $actualUrl = $this->extension->getCampImageUrl($campImage);
        $expectedUrl = sprintf('/files/dynamic/camp/%s?t=%s', $campImageFileName, $lastModified);

        $this->assertSame($expectedUrl, $actualUrl);

        // non-existent file

        $this->campImageStorage->delete($campImageFileName);

        $actualUrl = $this->extension->getCampImageUrl($campImage);
        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);
    }

    public function testGetPurchasableItemImageUrl(): void
    {
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItem->setImageExtension(null);

        // null file

        $actualUrl = $this->extension->getPurchasableItemImageUrl($purchasableItem);
        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);

        // non-existent file

        $purchasableItem->setImageExtension('png');
        $actualUrl = $this->extension->getPurchasableItemImageUrl($purchasableItem);
        $this->assertSame('/files/static/placeholder.jpg', $actualUrl);

        // existing file

        $purchasableItemIdString = $purchasableItem
            ->getId()
            ->toRfc4122()
        ;

        $purchasableItemImageFileName = $purchasableItemIdString . '.png';

        $this->purchasableItemImageStorage->write($purchasableItemImageFileName, 'Contents...');
        $lastModified = $this->purchasableItemImageStorage->lastModified($purchasableItemImageFileName);

        $actualUrl = $this->extension->getPurchasableItemImageUrl($purchasableItem);
        $expectedUrl = sprintf('/files/dynamic/purchasable-item/%s?t=%s', $purchasableItemImageFileName, $lastModified);

        $this->assertSame($expectedUrl, $actualUrl);
    }

    public function testGetUserImageUrl(): void
    {
        $user = new User('bob@gmail.com');
        $user->setImageExtension(null);

        // null file

        $actualUrl = $this->extension->getUserImageUrl($user);
        $this->assertSame('/files/static/user_placeholder.png', $actualUrl);

        // non-existent file

        $user->setImageExtension('png');
        $actualUrl = $this->extension->getUserImageUrl($user);
        $this->assertSame('/files/static/user_placeholder.png', $actualUrl);

        // existing file

        $userIdString = $user
            ->getId()
            ->toRfc4122()
        ;

        $userImageFileName = $userIdString . '.png';

        $this->userImageStorage->write($userImageFileName, 'Contents...');
        $lastModified = $this->userImageStorage->lastModified($userImageFileName);

        $actualUrl = $this->extension->getUserImageUrl($user);
        $expectedUrl = sprintf('/files/dynamic/user/%s?t=%s', $userImageFileName, $lastModified);

        $this->assertSame($expectedUrl, $actualUrl);
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var ImageExtension $extension */
        $extension = $container->get(ImageExtension::class);
        $this->extension = $extension;

        /** @var FilesystemOperator $campImageStorage */
        $campImageStorage = $container->get('camp_image.storage');
        $this->campImageStorage = $campImageStorage;

        /** @var FilesystemOperator $purchasableItemImageStorage */
        $purchasableItemImageStorage = $container->get('purchasable_item_image.storage');
        $this->purchasableItemImageStorage = $purchasableItemImageStorage;

        /** @var FilesystemOperator $userImageStorage */
        $userImageStorage = $container->get('user_image.storage');
        $this->userImageStorage = $userImageStorage;
    }
}