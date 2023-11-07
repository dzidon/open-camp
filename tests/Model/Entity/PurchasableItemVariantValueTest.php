<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemVariantValueTest extends TestCase
{
    private const NAME = 'name';
    private const PRIORITY = 100;

    private PurchasableItemVariantValue $purchasableItemVariantValue;
    private PurchasableItemVariant $purchasableItemVariant;
    private PurchasableItem $purchasableItem;

    public function testId(): void
    {
        $id = $this->purchasableItemVariantValue->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->purchasableItemVariantValue->getName());

        $newName = 'New name';
        $this->purchasableItemVariantValue->setName($newName);
        $this->assertSame($newName, $this->purchasableItemVariantValue->getName());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->purchasableItemVariantValue->getPriority());

        $newPriority = 200;
        $this->purchasableItemVariantValue->setPriority($newPriority);
        $this->assertSame($newPriority, $this->purchasableItemVariantValue->getPriority());
    }

    public function testPurchasableItemVariant(): void
    {
        $this->assertSame($this->purchasableItemVariant, $this->purchasableItemVariantValue->getPurchasableItemVariant());

        $newPurchasableItemVariant = new PurchasableItemVariant('New variant', 600, $this->purchasableItem);
        $this->purchasableItemVariantValue->setPurchasableItemVariant($newPurchasableItemVariant);
        $this->assertSame($newPurchasableItemVariant, $this->purchasableItemVariantValue->getPurchasableItemVariant());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->purchasableItemVariantValue->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->purchasableItemVariantValue->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 500, $this->purchasableItem);
        $this->purchasableItemVariantValue = new PurchasableItemVariantValue(self::NAME, self::PRIORITY, $this->purchasableItemVariant);
    }
}