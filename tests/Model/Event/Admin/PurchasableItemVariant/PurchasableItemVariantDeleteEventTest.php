<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariant;

use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantDeleteEvent;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantDeleteEventTest extends TestCase
{
    private PurchasableItem $purchasableItem;

    private PurchasableItemVariant $purchasableItemVariant;

    private PurchasableItemVariantDeleteEvent $event;

    public function testEntity(): void
    {
        $this->assertSame($this->purchasableItemVariant, $this->event->getPurchasableItemVariant());

        $newPurchasableItemVariant = new PurchasableItemVariant('Variant new', 600, $this->purchasableItem);
        $this->event->setPurchasableItemVariant($newPurchasableItemVariant);
        $this->assertSame($newPurchasableItemVariant, $this->event->getPurchasableItemVariant());
    }

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 500, $this->purchasableItem);
        $this->event = new PurchasableItemVariantDeleteEvent($this->purchasableItemVariant);
    }
}