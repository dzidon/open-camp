<?php

namespace App\Tests\Service\Twig;

use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Service\Twig\ImageExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class ImageExtensionTest extends KernelTestCase
{
    private ImageExtension $extension;

    private CampImageRepositoryInterface $campImageRepository;

    private PurchasableItemRepositoryInterface $purchasableItemRepository;

    public function testCampImagePath(): void
    {
        $id = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campImage = $this->campImageRepository->findOneById($id);
        $path = $this->extension->getCampImagePath($campImage);

        $this->assertSame('files/dynamic/camp/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b.jpg?t=0', $path);
    }

    public function testNullCampImagePath(): void
    {
        $path = $this->extension->getCampImagePath(null);

        $this->assertSame('files/static/camp/placeholder.jpg?t=0', $path);
    }

    public function testPurchasableItemImagePath(): void
    {
        $id = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $purchasableItem = $this->purchasableItemRepository->findOneById($id);
        $path = $this->extension->getPurchasableItemImagePath($purchasableItem);

        $this->assertSame('files/dynamic/purchasable-item/e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b.jpg?t=0', $path);
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var ImageExtension $extension */
        $extension = $container->get(ImageExtension::class);
        $this->extension = $extension;

        /** @var CampImageRepositoryInterface $campImageRepository */
        $campImageRepository = $container->get(CampImageRepositoryInterface::class);
        $this->campImageRepository = $campImageRepository;

        /** @var PurchasableItemRepositoryInterface $purchasableItemRepository */
        $purchasableItemRepository = $container->get(PurchasableItemRepositoryInterface::class);
        $this->purchasableItemRepository = $purchasableItemRepository;
    }
}