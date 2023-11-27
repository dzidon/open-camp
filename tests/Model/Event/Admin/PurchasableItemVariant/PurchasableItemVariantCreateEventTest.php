<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Model\Entity\PurchasableItem;
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

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->data = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->event = new PurchasableItemVariantCreateEvent($this->data);
    }
}