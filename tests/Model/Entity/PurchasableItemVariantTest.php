<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemVariantTest extends TestCase
{
    private const NAME = 'name';
    private const PRIORITY = 100;

    private PurchasableItemVariant $purchasableItemVariant;
    private PurchasableItem $purchasableItem;

    public function testId(): void
    {
        $id = $this->purchasableItemVariant->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->purchasableItemVariant->getName());

        $newName = 'New name';
        $this->purchasableItemVariant->setName($newName);
        $this->assertSame($newName, $this->purchasableItemVariant->getName());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->purchasableItemVariant->getPriority());

        $newPriority = 200;
        $this->purchasableItemVariant->setPriority($newPriority);
        $this->assertSame($newPriority, $this->purchasableItemVariant->getPriority());
    }

    public function testPurchasableItem(): void
    {
        $this->assertSame($this->purchasableItem, $this->purchasableItemVariant->getPurchasableItem());

        $newPurchasableItem = new PurchasableItem('New item', 'Label', 2000.0, 20);
        $this->purchasableItemVariant->setPurchasableItem($newPurchasableItem);
        $this->assertSame($newPurchasableItem, $this->purchasableItemVariant->getPurchasableItem());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->purchasableItemVariant->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->purchasableItemVariant->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant(self::NAME, self::PRIORITY, $this->purchasableItem);
    }
}