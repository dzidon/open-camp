<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItem;
use LogicException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchasableItemVariantCreationDataTest extends KernelTestCase
{
    private PurchasableItemVariantCreationData $data;
    private PurchasableItem $purchasableItem;
    private ValidatorInterface $validator;

    public function testPurchasableItemVariantData(): void
    {
        $variantData = $this->data->getPurchasableItemVariantData();
        $this->assertInstanceOf(PurchasableItemVariantData::class, $variantData);
        $this->assertSame($this->purchasableItem, $variantData->getPurchasableItem());
    }

    public function testPurchasableItemVariantDataValidation(): void
    {
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantData');
        $this->assertNotEmpty($result); // invalid

        $variantData = $this->data->getPurchasableItemVariantData();
        $variantData->setName('text');
        $variantData->setPriority(100);
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantData');
        $this->assertEmpty($result); // valid
    }

    public function testPurchasableItemVariantValuesData(): void
    {
        $this->assertEmpty($this->data->getPurchasableItemVariantValuesData());

        $newPurchasableItemVariantValuesData = [
            new PurchasableItemVariantValueData(),
            new PurchasableItemVariantValueData(),
        ];

        foreach ($newPurchasableItemVariantValuesData as $newPurchasableItemVariantValueData)
        {
            $this->data->addPurchasableItemVariantValueData($newPurchasableItemVariantValueData);
        }

        $this->assertSame($newPurchasableItemVariantValuesData, $this->data->getPurchasableItemVariantValuesData());

        $this->data->removePurchasableItemVariantValueData($newPurchasableItemVariantValuesData[0]);
        $this->assertNotContains($newPurchasableItemVariantValuesData[0], $this->data->getPurchasableItemVariantValuesData());

        $this->data = new PurchasableItemVariantCreationData($this->purchasableItem);
        $this->data->setPurchasableItemVariantValuesData($newPurchasableItemVariantValuesData);
        $this->assertSame($newPurchasableItemVariantValuesData, $this->data->getPurchasableItemVariantValuesData());

        $this->expectException(LogicException::class);
        $newPurchasableItemVariantValuesData = [new stdClass()];
        $this->data->setPurchasableItemVariantValuesData($newPurchasableItemVariantValuesData);
    }

    public function testPurchasableItemVariantValuesDataValidation(): void
    {
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantValuesData');
        $this->assertNotEmpty($result); // invalid

        $valueData = new PurchasableItemVariantValueData();
        $this->data->addPurchasableItemVariantValueData($valueData);
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantValuesData');
        $this->assertNotEmpty($result); // invalid

        $valueData->setName('Value');
        $valueData->setPriority(100);
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantValuesData');
        $this->assertEmpty($result); // valid

        $anotherValueData = new PurchasableItemVariantValueData();
        $anotherValueData->setName('Value');
        $anotherValueData->setPriority(200);
        $this->data->addPurchasableItemVariantValueData($anotherValueData);
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantValuesData');
        $this->assertNotEmpty($result); // invalid

        $anotherValueData->setName('Another value');
        $result = $this->validator->validateProperty($this->data, 'purchasableItemVariantValuesData');
        $this->assertEmpty($result); // valid
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);
        $this->validator = $validator;

        $this->purchasableItem = new PurchasableItem('Item', 'Label', 100.0, 50);
        $this->data = new PurchasableItemVariantCreationData($this->purchasableItem);
    }
}