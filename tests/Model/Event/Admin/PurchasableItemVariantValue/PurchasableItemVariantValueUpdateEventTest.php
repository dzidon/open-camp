<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariantValue\PurchasableItemVariantValueUpdateEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantValueUpdateEventTest extends TestCase
{
    private PurchasableItemVariant $purchasableItemVariant;

    private PurchasableItemVariantValue $purchasableItemVariantValue;

    private PurchasableItemVariantValueData $data;

    private PurchasableItemVariantValueUpdateEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantValueData());

        $newData = new PurchasableItemVariantValueData();
        $this->event->setPurchasableItemVariantValueData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantValueData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->purchasableItemVariantValue, $this->event->getPurchasableItemVariantValue());

        $newPurchasableItemVariantValue = new PurchasableItemVariantValue('Value new', 200, $this->purchasableItemVariant);
        $this->event->setPurchasableItemVariantValue($newPurchasableItemVariantValue);
        $this->assertSame($newPurchasableItemVariantValue, $this->event->getPurchasableItemVariantValue());
    }

    protected function setUp(): void
    {
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $this->purchasableItemVariantValue = new PurchasableItemVariantValue('Value', 100, $this->purchasableItemVariant);
        $this->data = new PurchasableItemVariantValueData();
        $this->event = new PurchasableItemVariantValueUpdateEvent($this->data, $this->purchasableItemVariantValue);
    }
}