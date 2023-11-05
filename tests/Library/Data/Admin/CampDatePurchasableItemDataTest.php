<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\PurchasableItem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDatePurchasableItemDataTest extends KernelTestCase
{
    public function testPurchasableItem(): void
    {
        $data = new CampDatePurchasableItemData();
        $this->assertNull($data->getPurchasableItem());

        $purchasableItem = new PurchasableItem('Item', 1000.0, 2);
        $data->setPurchasableItem($purchasableItem);
        $this->assertSame($purchasableItem, $data->getPurchasableItem());

        $data->setPurchasableItem(null);
        $this->assertNull($data->getPurchasableItem());
    }

    public function testPurchasableItemValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDatePurchasableItemData();
        $result = $validator->validateProperty($data, 'purchasableItem');
        $this->assertNotEmpty($result); // invalid

        $purchasableItem = new PurchasableItem('Item', 1000.0, 2);
        $data->setPurchasableItem($purchasableItem);
        $result = $validator->validateProperty($data, 'purchasableItem');
        $this->assertEmpty($result); // valid

        $data->setPurchasableItem(null);
        $result = $validator->validateProperty($data, 'purchasableItem');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPriority(): void
    {
        $data = new CampDatePurchasableItemData();
        $this->assertSame(0, $data->getPriority());

        $data->setPriority(100);
        $this->assertSame(100, $data->getPriority());

        $data->setPriority(null);
        $this->assertNull($data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDatePurchasableItemData();
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(100);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(null);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertNotEmpty($result); // invalid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}