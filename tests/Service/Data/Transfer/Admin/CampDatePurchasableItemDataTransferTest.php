<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Entity\PurchasableItem;
use App\Service\Data\Transfer\Admin\CampDatePurchasableItemDataTransfer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampDatePurchasableItemDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDatePurchasableItemDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $purchasableItem = new PurchasableItem('Item', 1000.0, 10);
        $campDatePurchasableItem = new CampDatePurchasableItem($campDate, $purchasableItem, $expectedPriority);

        $data = new CampDatePurchasableItemData();
        $dataTransfer->fillData($data, $campDatePurchasableItem);

        $this->assertSame($purchasableItem, $data->getPurchasableItem());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDatePurchasableItemDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $purchasableItem = new PurchasableItem('Item', 1000.0, 10);
        $campDatePurchasableItem = new CampDatePurchasableItem($campDate, $purchasableItem, $expectedPriority);

        $data = new CampDatePurchasableItemData();
        $data->setPriority($expectedPriority);
        $data->setPurchasableItem($purchasableItem);
        $dataTransfer->fillEntity($data, $campDatePurchasableItem);

        $this->assertSame($purchasableItem, $campDatePurchasableItem->getPurchasableItem());
        $this->assertSame($expectedPriority, $campDatePurchasableItem->getPriority());
    }

    private function getCampDatePurchasableItemDataTransfer(): CampDatePurchasableItemDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDatePurchasableItemDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDatePurchasableItemDataTransfer::class);

        return $dataTransfer;
    }
}