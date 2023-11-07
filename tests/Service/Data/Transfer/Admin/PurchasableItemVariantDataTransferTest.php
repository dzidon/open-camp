<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Service\Data\Transfer\Admin\PurchasableItemVariantDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemVariantDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getPurchasableItemVariantDataTransfer();

        $expectedName = 'Name';
        $expectedPriority = 100;
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant($expectedName, $expectedPriority, $purchasableItem);

        $data = new PurchasableItemVariantData($purchasableItem);
        $dataTransfer->fillData($data, $purchasableItemVariant);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getPurchasableItemVariantDataTransfer();

        $expectedName = 'Name';
        $expectedPriority = 100;
        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant('', 0, $purchasableItem);

        $data = new PurchasableItemVariantData($purchasableItem);
        $data->setName($expectedName);
        $data->setPriority($expectedPriority);
        $dataTransfer->fillEntity($data, $purchasableItemVariant);

        $this->assertSame($expectedName, $purchasableItemVariant->getName());
        $this->assertSame($expectedPriority, $purchasableItemVariant->getPriority());
    }

    private function getPurchasableItemVariantDataTransfer(): PurchasableItemVariantDataTransfer
    {
        $container = static::getContainer();

        /** @var PurchasableItemVariantDataTransfer $dataTransfer */
        $dataTransfer = $container->get(PurchasableItemVariantDataTransfer::class);

        return $dataTransfer;
    }
}