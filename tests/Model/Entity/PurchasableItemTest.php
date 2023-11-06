<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\PurchasableItem;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemTest extends TestCase
{
    private const NAME = 'name';
    private const PRICE = 1000.0;
    private const MAX_AMOUNT = 10;

    private PurchasableItem $purchasableItem;

    public function testId(): void
    {
        $id = $this->purchasableItem->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->purchasableItem->getName());

        $newName = 'New name';
        $this->purchasableItem->setName($newName);
        $this->assertSame($newName, $this->purchasableItem->getName());
    }

    public function testPrice(): void
    {
        $this->assertSame(self::PRICE, $this->purchasableItem->getPrice());

        $newPrice = 2000.0;
        $this->purchasableItem->setPrice($newPrice);
        $this->assertSame($newPrice, $this->purchasableItem->getPrice());
    }

    public function testMaxAmount(): void
    {
        $this->assertSame(self::MAX_AMOUNT, $this->purchasableItem->getMaxAmount());

        $newMaxAmount = 20;
        $this->purchasableItem->setMaxAmount($newMaxAmount);
        $this->assertSame($newMaxAmount, $this->purchasableItem->getMaxAmount());
    }

    public function testIsGlobal(): void
    {
        $this->assertFalse($this->purchasableItem->isGlobal());

        $this->purchasableItem->setIsGlobal(true);
        $this->assertTrue($this->purchasableItem->isGlobal());

        $this->purchasableItem->setIsGlobal(false);
        $this->assertFalse($this->purchasableItem->isGlobal());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->purchasableItem->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->purchasableItem->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem(self::NAME, self::PRICE, self::MAX_AMOUNT);
    }
}