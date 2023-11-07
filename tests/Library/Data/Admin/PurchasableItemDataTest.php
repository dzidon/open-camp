<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchasableItemDataTest extends KernelTestCase
{
    public function testPurchasableItem(): void
    {
        $data = new PurchasableItemData();
        $this->assertNull($data->getPurchasableItem());

        $purchasableItem = new PurchasableItem('Item', 'Item...', 100.0, 5);
        $data = new PurchasableItemData($purchasableItem);
        $this->assertSame($purchasableItem, $data->getPurchasableItem());
    }

    public function testName(): void
    {
        $data = new PurchasableItemData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PurchasableItemData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName('');
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(null);
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testLabel(): void
    {
        $data = new PurchasableItemData();
        $this->assertNull($data->getLabel());

        $data->setLabel('label');
        $this->assertSame('label', $data->getLabel());

        $data->setLabel(null);
        $this->assertNull($data->getLabel());
    }

    public function testLabelValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PurchasableItemData();
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('');
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(null);
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'label');
        $this->assertEmpty($result); // valid

        $data->setLabel(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPrice(): void
    {
        $data = new PurchasableItemData();
        $this->assertNull($data->getPrice());

        $data->setPrice(100.0);
        $this->assertSame(100.0, $data->getPrice());

        $data->setPrice(null);
        $this->assertNull($data->getPrice());
    }

    public function testPriceValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PurchasableItemData();
        $result = $validator->validateProperty($data, 'price');
        $this->assertNotEmpty($result); // invalid

        $data->setPrice(null);
        $result = $validator->validateProperty($data, 'price');
        $this->assertNotEmpty($result); // invalid

        $data->setPrice(-1.0);
        $result = $validator->validateProperty($data, 'price');
        $this->assertNotEmpty($result); // invalid

        $data->setPrice(0.0);
        $result = $validator->validateProperty($data, 'price');
        $this->assertEmpty($result); // valid

        $data->setPrice(1.0);
        $result = $validator->validateProperty($data, 'price');
        $this->assertEmpty($result); // valid
    }

    public function testIsGlobal(): void
    {
        $data = new PurchasableItemData();
        $this->assertFalse($data->isGlobal());

        $data->setIsGlobal(true);
        $this->assertTrue($data->isGlobal());

        $data->setIsGlobal(false);
        $this->assertFalse($data->isGlobal());
    }

    public function testMaxAmount(): void
    {
        $data = new PurchasableItemData();
        $this->assertNull($data->getMaxAmount());

        $data->setMaxAmount(10);
        $this->assertSame(10, $data->getMaxAmount());

        $data->setMaxAmount(null);
        $this->assertNull($data->getMaxAmount());
    }

    public function testMaxAmountValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PurchasableItemData();
        $result = $validator->validateProperty($data, 'maxAmount');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxAmount(null);
        $result = $validator->validateProperty($data, 'maxAmount');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxAmount(-1);
        $result = $validator->validateProperty($data, 'maxAmount');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxAmount(0);
        $result = $validator->validateProperty($data, 'maxAmount');
        $this->assertNotEmpty($result); // invalid

        $data->setMaxAmount(1);
        $result = $validator->validateProperty($data, 'maxAmount');
        $this->assertEmpty($result); // valid
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new PurchasableItemData();
        $data->setPrice(100.0);
        $data->setMaxAmount(10);
        $data->setLabel('Label');
        $data->setName('Item 1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $item = $purchasableItemRepository->findOneByName('Item 2');
        $data = new PurchasableItemData($item);
        $data->setPrice(100.0);
        $data->setMaxAmount(10);
        $data->setLabel('Label');
        $data->setName('Item 2');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setName('Item 1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setName('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getPurchasableItemRepository(): PurchasableItemRepositoryInterface
    {
        $container = static::getContainer();

        /** @var PurchasableItemRepositoryInterface $repository */
        $repository = $container->get(PurchasableItemRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}