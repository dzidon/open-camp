<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariantValue;

use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueDeleteEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantValueDeleteEventTest extends TestCase
{
    private PurchasableItemVariant $purchasableItemVariant;

    private PurchasableItemVariantValue $purchasableItemVariantValue;

    private PurchasableItemVariantValueDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->purchasableItemVariantValue, $this->event->getPurchasableItemVariantValue());

        $newPurchasableItemVariantValue = new PurchasableItemVariantValue('Value new', 200, $this->purchasableItemVariant);
        $this->event->setPurchasableItemVariantValue($newPurchasableItemVariantValue);
        $this->assertSame($newPurchasableItemVariantValue, $this->event->getPurchasableItemVariantValue());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    protected function setUp(): void
    {
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $this->purchasableItemVariantValue = new PurchasableItemVariantValue('Value', 100, $this->purchasableItemVariant);
        $this->event = new PurchasableItemVariantValueDeleteEvent($this->purchasableItemVariantValue);
    }
}