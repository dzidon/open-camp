<?php

namespace App\Tests\Model\Event\Admin\PurchasableItem;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemCreatedEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemCreatedEventTest extends TestCase
{
    private PurchasableItem $entity;

    private PurchasableItemData $data;

    private PurchasableItemCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemData());

        $newData = new PurchasableItemData();
        $this->event->setPurchasableItemData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getPurchasableItem());

        $newEntity = new PurchasableItem('Item new', 'Label', 2000.0, 3);
        $this->event->setPurchasableItem($newEntity);
        $this->assertSame($newEntity, $this->event->getPurchasableItem());
    }

    protected function setUp(): void
    {
        $this->entity = new PurchasableItem('Item', 'Label', 1000.0, 2);
        $this->data = new PurchasableItemData();
        $this->event = new PurchasableItemCreatedEvent($this->data, $this->entity);
    }
}