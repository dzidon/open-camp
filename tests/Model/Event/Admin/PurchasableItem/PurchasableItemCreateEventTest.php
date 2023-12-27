<?php

namespace App\Tests\Model\Event\Admin\PurchasableItem;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemCreateEventTest extends TestCase
{
    private PurchasableItemData $data;

    private PurchasableItemCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemData());

        $newData = new PurchasableItemData();
        $this->event->setPurchasableItemData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemData());
    }

    public function testEntity(): void
    {
        $this->assertNull($this->event->getPurchasableItem());

        $newEntity = new PurchasableItem('Item new', 'Label', 2000.0, 3);
        $this->event->setPurchasableItem($newEntity);
        $this->assertSame($newEntity, $this->event->getPurchasableItem());
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
        $this->data = new PurchasableItemData();
        $this->event = new PurchasableItemCreateEvent($this->data);
    }
}