<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Repository\CampDatePurchasableItemRepository;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDatePurchasableItemRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campDatePurchasableItemRepository = $this->getCampDatePurchasableItemRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('550e8400-e29b-41d4-a716-446655440000'));
        $purchasableItem = $purchasableItemRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $campDatePurchasableItem = new CampDatePurchasableItem($campDate, $purchasableItem, 300);
        $campDatePurchasableItemRepository->saveCampDatePurchasableItem($campDatePurchasableItem, true);

        $loadedCampDatePurchasableItems = $campDatePurchasableItemRepository->findByCampDate($campDate);
        $this->assertCount(1, $loadedCampDatePurchasableItems);
        $loadedCampDatePurchasableItem = $loadedCampDatePurchasableItems[0];
        $this->assertSame($purchasableItem, $loadedCampDatePurchasableItem->getPurchasableItem());
        $this->assertSame($campDate, $loadedCampDatePurchasableItem->getCampDate());

        $campDatePurchasableItemRepository->removeCampDatePurchasableItem($campDatePurchasableItem, true);
        $loadedCampDatePurchasableItems = $campDatePurchasableItemRepository->findByCampDate($campDate);
        $this->assertCount(0, $loadedCampDatePurchasableItems);
    }

    public function testFindByCampDate(): void
    {
        $campDatePurchasableItemRepository = $this->getCampDatePurchasableItemRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $loadedCampDatePurchasableItems = $campDatePurchasableItemRepository->findByCampDate($campDate);
        $this->assertCount(2, $loadedCampDatePurchasableItems);
    }

    private function getPurchasableItemRepository(): PurchasableItemRepositoryInterface
    {
        $container = static::getContainer();

        /** @var PurchasableItemRepositoryInterface $repository */
        $repository = $container->get(PurchasableItemRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampDateRepositoryInterface $repository */
        $repository = $container->get(CampDateRepositoryInterface::class);

        return $repository;
    }

    private function getCampDatePurchasableItemRepository(): CampDatePurchasableItemRepository
    {
        $container = static::getContainer();

        /** @var CampDatePurchasableItemRepository $repository */
        $repository = $container->get(CampDatePurchasableItemRepository::class);

        return $repository;
    }
}