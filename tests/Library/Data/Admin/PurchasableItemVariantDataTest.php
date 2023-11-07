<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PurchasableItemVariantDataTest extends KernelTestCase
{
    private PurchasableItemVariantData $data;
    private PurchasableItem $purchasableItem;
    private PurchasableItemVariant $purchasableItemVariant;
    private ValidatorInterface $validator;
    private PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository;

    public function testPurchasableItem(): void
    {
        $this->assertSame($this->purchasableItem, $this->data->getPurchasableItem());
    }

    public function testPurchasableItemVariant(): void
    {
        $this->assertSame($this->purchasableItemVariant, $this->data->getPurchasableItemVariant());
    }

    public function testName(): void
    {
        $this->assertNull($this->data->getName());

        $this->data->setName('text');
        $this->assertSame('text', $this->data->getName());

        $this->data->setName(null);
        $this->assertNull($this->data->getName());
    }

    public function testNameValidation(): void
    {
        $result = $this->validator->validateProperty($this->data, 'name');
        $this->assertNotEmpty($result); // invalid

        $this->data->setName('');
        $result = $this->validator->validateProperty($this->data, 'name');
        $this->assertNotEmpty($result); // invalid

        $this->data->setName(null);
        $result = $this->validator->validateProperty($this->data, 'name');
        $this->assertNotEmpty($result); // invalid

        $this->data->setName(str_repeat('x', 255));
        $result = $this->validator->validateProperty($this->data, 'name');
        $this->assertEmpty($result); // valid

        $this->data->setName(str_repeat('x', 256));
        $result = $this->validator->validateProperty($this->data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPriority(): void
    {
        $this->assertSame(0, $this->data->getPriority());

        $this->data->setPriority(100);
        $this->assertSame(100, $this->data->getPriority());

        $this->data->setPriority(null);
        $this->assertNull($this->data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $result = $this->validator->validateProperty($this->data, 'priority');
        $this->assertEmpty($result); // valid

        $this->data->setPriority(100);
        $result = $this->validator->validateProperty($this->data, 'priority');
        $this->assertEmpty($result); // valid

        $this->data->setPriority(null);
        $result = $this->validator->validateProperty($this->data, 'priority');
        $this->assertNotEmpty($result); // invalid
    }

    public function testUniqueValidation(): void
    {
        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $variant = $this->purchasableItemVariantRepository->findOneById($uid);

        $data = new PurchasableItemVariantData($variant->getPurchasableItem(), $variant);
        $data->setPriority(100);

        // editing (no change of name)
        $data->setName('Variant 1');
        $result = $this->validator->validate($data);
        $this->assertEmpty($result); // valid

        // editing (new name)
        $data->setName('Variant 3');
        $result = $this->validator->validate($data);
        $this->assertEmpty($result); // valid

        // editing (existing name)
        $data->setName('Variant 2');
        $result = $this->validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data = new PurchasableItemVariantData($variant->getPurchasableItem());
        $data->setPriority(100);

        // new (new name)
        $data->setName('Variant 3');
        $result = $this->validator->validate($data);
        $this->assertEmpty($result); // valid

        // new (existing name)
        $data->setName('Variant 2');
        $result = $this->validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);
        $this->validator = $validator;

        /** @var PurchasableItemVariantRepositoryInterface $repository */
        $repository = $container->get(PurchasableItemVariantRepositoryInterface::class);
        $this->purchasableItemVariantRepository = $repository;

        $this->purchasableItem = new PurchasableItem('Item', 'Label', 100.0, 50);
        $this->purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $this->purchasableItem);
        $this->data = new PurchasableItemVariantData($this->purchasableItem, $this->purchasableItemVariant);
    }
}