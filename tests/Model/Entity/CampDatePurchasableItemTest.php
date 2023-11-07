<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Entity\PurchasableItem;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampDatePurchasableItemTest extends TestCase
{
    private const PRIORITY = 100;

    private Camp $camp;
    private CampDate $campDate;
    private PurchasableItem $purchasableItem;
    private CampDatePurchasableItem $campDatePurchasableItem;

    public function testId(): void
    {
        $id = $this->campDatePurchasableItem->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testCampDate(): void
    {
        $this->assertSame($this->campDate, $this->campDatePurchasableItem->getCampDate());
        $this->assertContains($this->campDatePurchasableItem, $this->campDate->getCampDatePurchasableItems());

        $campDateNew = new CampDate(new DateTimeImmutable('2000-01-02'), new DateTimeImmutable('2000-01-08'), 2000.0, 20, $this->camp);
        $this->campDatePurchasableItem->setCampDate($campDateNew);

        $this->assertSame($campDateNew, $this->campDatePurchasableItem->getCampDate());
        $this->assertContains($this->campDatePurchasableItem, $campDateNew->getCampDatePurchasableItems());
        $this->assertNotContains($this->campDatePurchasableItem, $this->campDate->getCampDatePurchasableItems());
    }

    public function testPurchasableItem(): void
    {
        $this->assertSame($this->purchasableItem, $this->campDatePurchasableItem->getPurchasableItem());

        $purchasableItemNew = new PurchasableItem('Item new', 'Label', 2000.0, 20);
        $this->campDatePurchasableItem->setPurchasableItem($purchasableItemNew);
        $this->assertSame($purchasableItemNew, $this->campDatePurchasableItem->getPurchasableItem());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->campDatePurchasableItem->getPriority());

        $newPriority = 200;
        $this->campDatePurchasableItem->setPriority($newPriority);
        $this->assertSame($newPriority, $this->campDatePurchasableItem->getPriority());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->campDatePurchasableItem->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->campDatePurchasableItem->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $this->campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $this->camp);
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->campDatePurchasableItem = new CampDatePurchasableItem($this->campDate, $this->purchasableItem, 100);
    }
}