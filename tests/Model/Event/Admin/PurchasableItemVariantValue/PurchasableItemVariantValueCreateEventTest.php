<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueCreateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantValueCreateEventTest extends TestCase
{
    private PurchasableItemVariantValueData $data;

    private PurchasableItemVariantValueCreateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantValueData());

        $newData = new PurchasableItemVariantValueData();
        $this->event->setPurchasableItemVariantValueData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantValueData());
    }

    protected function setUp(): void
    {
        $this->data = new PurchasableItemVariantValueData();
        $this->event = new PurchasableItemVariantValueCreateEvent($this->data);
    }
}