<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Service\Data\Transfer\Admin\PurchasableItemVariantValueDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemVariantValueDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getPurchasableItemVariantValueDataTransfer();

        $expectedName = 'Name';
        $expectedPriority = 100;
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $purchasableItemVariantValue = new PurchasableItemVariantValue($expectedName, $expectedPriority, $purchasableItemVariant);

        $data = new PurchasableItemVariantValueData($purchasableItemVariantValue);
        $dataTransfer->fillData($data, $purchasableItemVariantValue);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getPurchasableItemVariantValueDataTransfer();

        $expectedName = 'Name';
        $expectedPriority = 100;
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $purchasableItemVariantValue = new PurchasableItemVariantValue('', 0, $purchasableItemVariant);

        $data = new PurchasableItemVariantValueData($purchasableItemVariantValue);
        $data->setName($expectedName);
        $data->setPriority($expectedPriority);
        $dataTransfer->fillEntity($data, $purchasableItemVariantValue);

        $this->assertSame($expectedName, $purchasableItemVariantValue->getName());
        $this->assertSame($expectedPriority, $purchasableItemVariantValue->getPriority());
    }

    private function getPurchasableItemVariantValueDataTransfer(): PurchasableItemVariantValueDataTransfer
    {
        $container = static::getContainer();

        /** @var PurchasableItemVariantValueDataTransfer $dataTransfer */
        $dataTransfer = $container->get(PurchasableItemVariantValueDataTransfer::class);

        return $dataTransfer;
    }
}