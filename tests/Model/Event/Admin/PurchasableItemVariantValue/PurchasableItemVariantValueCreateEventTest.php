<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantValueCreateEventTest extends TestCase
{
    private PurchasableItemVariantValueData $data;

    private PurchasableItemVariantValueCreateEvent $event;

    private PurchasableItemVariant $purchasableItemVariant;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantValueData());

        $newData = new PurchasableItemVariantValueData();
        $this->event->setPurchasableItemVariantValueData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantValueData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getPurchasableItemVariantValue());

        $newEntity = new PurchasableItemVariantValue('Value new', 200, $this->purchasableItemVariant);
        $this->event->setPurchasableItemVariantValue($newEntity);
        $this->assertSame($newEntity, $this->event->getPurchasableItemVariantValue());
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
        $this->data = new PurchasableItemVariantValueData();
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant new', 600, $purchasableItem);
        $this->event = new PurchasableItemVariantValueCreateEvent($this->data, $this->purchasableItemVariant);
    }
}