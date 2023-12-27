<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantCreateEventTest extends TestCase
{
    private PurchasableItemVariantCreationData $data;

    private PurchasableItemVariantCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantCreationData());

        $newData = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->event->setPurchasableItemVariantCreationData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantCreationData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getPurchasableItemVariant());

        $newEntity = new PurchasableItemVariant('Variant new', 600, $this->purchasableItem);
        $this->event->setPurchasableItemVariant($newEntity);
        $this->assertSame($newEntity, $this->event->getPurchasableItemVariant());
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
        $this->data = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->event = new PurchasableItemVariantCreateEvent($this->data);
    }
}