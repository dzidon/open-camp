<?php

namespace App\Tests\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\Admin\PurchasableItemVariant\PurchasableItemVariantCreatedEvent;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class PurchasableItemVariantCreatedEventTest extends TestCase
{
    private PurchasableItem $purchasableItem;

    private PurchasableItemVariant $purchasableItemVariant;

    /**
     * @var PurchasableItemVariant[]
     */
    private array $purchasableItemVariantValues;

    private PurchasableItemVariantCreationData $data;

    private PurchasableItemVariantCreatedEvent $event;

    public function testData(): void
    {
        $this->assertSame($this->data, $this->event->getPurchasableItemVariantCreationData());

        $newData = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->event->setPurchasableItemVariantCreationData($newData);
        $this->assertSame($newData, $this->event->getPurchasableItemVariantCreationData());
    }

    public function testEntity(): void
    {
        $this->assertSame($this->purchasableItemVariant, $this->event->getPurchasableItemVariant());

        $newPurchasableItemVariant = new PurchasableItemVariant('Variant new', 600, $this->purchasableItem);
        $this->event->setPurchasableItemVariant($newPurchasableItemVariant);
        $this->assertSame($newPurchasableItemVariant, $this->event->getPurchasableItemVariant());
    }

    public function testPurchasableItemVariantValues(): void
    {
        $this->assertSame($this->purchasableItemVariantValues, $this->event->getPurchasableItemVariantValues());

        $newPurchasableItemVariantValue = new PurchasableItemVariantValue('Value 3', 300, $this->purchasableItemVariant);
        $this->event->addPurchasableItemVariantValue($newPurchasableItemVariantValue);

        $purchasableItemVariantValues = $this->event->getPurchasableItemVariantValues();
        $this->assertContains($newPurchasableItemVariantValue, $purchasableItemVariantValues);

        $this->event->removePurchasableItemVariantValue($newPurchasableItemVariantValue);
        $purchasableItemVariantValues = $this->event->getPurchasableItemVariantValues();
        $this->assertNotContains($newPurchasableItemVariantValue, $purchasableItemVariantValues);
    }

    public function testInvalidPurchasableItemVariantValues(): void
    {
        $this->expectException(LogicException::class);
        new PurchasableItemVariantCreatedEvent($this->data, $this->purchasableItemVariant, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 500, $this->purchasableItem);
        $this->purchasableItemVariantValues = [
            new PurchasableItemVariantValue('Value 1', 100, $this->purchasableItemVariant),
            new PurchasableItemVariantValue('Value 2', 200, $this->purchasableItemVariant),
        ];
        $this->data = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->event = new PurchasableItemVariantCreatedEvent($this->data, $this->purchasableItemVariant, $this->purchasableItemVariantValues);
    }
}