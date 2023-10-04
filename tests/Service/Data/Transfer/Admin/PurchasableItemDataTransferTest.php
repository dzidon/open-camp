<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Service\Data\Transfer\Admin\PurchasableItemDataTransfer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PurchasableItemDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();

        $expectedName = 'Name';
        $expectedPrice = 1000.0;
        $maxAmountPerCamper = 10;
        $purchasableItem = new PurchasableItem($expectedName, $expectedPrice, $maxAmountPerCamper);

        $data = new PurchasableItemData();
        $dataTransfer->fillData($data, $purchasableItem);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($maxAmountPerCamper, $data->getMaxAmountPerCamper());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getPurchasableItemDataTransfer();

        $expectedName = 'Name';
        $expectedPrice = 1000.0;
        $maxAmountPerCamper = 10;
        $purchasableItem = new PurchasableItem('', 0.0, 0);

        $data = new PurchasableItemData();
        $data->setName($expectedName);
        $data->setPrice($expectedPrice);
        $data->setMaxAmountPerCamper($maxAmountPerCamper);
        $dataTransfer->fillEntity($data, $purchasableItem);

        $this->assertSame($expectedName, $purchasableItem->getName());
        $this->assertSame($expectedPrice, $purchasableItem->getPrice());
        $this->assertSame($maxAmountPerCamper, $purchasableItem->getMaxAmountPerCamper());
    }

    private function getPurchasableItemDataTransfer(): PurchasableItemDataTransfer
    {
        $container = static::getContainer();

        /** @var PurchasableItemDataTransfer $dataTransfer */
        $dataTransfer = $container->get(PurchasableItemDataTransfer::class);

        return $dataTransfer;
    }
}