<?php

namespace App\Tests\Model\Event\Admin\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Event\Admin\PurchasableItem\PurchasableItemDeleteEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemDeleteEventTest extends TestCase
{
    private PurchasableItem $entity;

    private PurchasableItemDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->entity, $this->event->getPurchasableItem());

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
        $this->entity = new PurchasableItem('Item', 'Label', 1000.0, 2);
        $this->event = new PurchasableItemDeleteEvent($this->entity);
    }
}