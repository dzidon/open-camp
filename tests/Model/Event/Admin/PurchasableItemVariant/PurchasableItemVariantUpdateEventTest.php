<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantUpdateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantUpdateEventTest extends TestCase
{
    private PurchasableItem $purchasableItem;

    private PurchasableItemVariant $purchasableItemVariant;

    private PurchasableItemVariantData $data;

    private PurchasableItemVariantUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantData());

        $newData = new PurchasableItemVariantData($this->purchasableItem);
        $this->event->setPurchasableItemVariantData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->purchasableItemVariant, $this->event->getPurchasableItemVariant());

        $newPurchasableItemVariant = new PurchasableItemVariant('Variant new', 600, $this->purchasableItem);
        $this->event->setPurchasableItemVariant($newPurchasableItemVariant);
        $this->assertSame($newPurchasableItemVariant, $this->event->getPurchasableItemVariant());
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
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 500, $this->purchasableItem);
        $this->purchasableItemVariantValues = [
            new PurchasableItemVariantValue('Value 1', 100, $this->purchasableItemVariant),
            new PurchasableItemVariantValue('Value 2', 200, $this->purchasableItemVariant),
        ];
        $this->data = new PurchasableItemVariantData($this->purchasableItem);
        $this->event = new PurchasableItemVariantUpdateEvent($this->data, $this->purchasableItemVariant);
    }
}