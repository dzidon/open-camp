<?php

namespace App\Tests\Model\Event\Admin\PurchasableItem;

use App\Library\Data\Admin\PurchasableItemData;
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

    protected function setUp(): void
    {
        $this->data = new PurchasableItemData();
        $this->event = new PurchasableItemCreateEvent($this->data);
    }
}