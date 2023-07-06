<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\BillingData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BillingDataTest extends KernelTestCase
{
    public function testName(): void
    {
        $data = new BillingData();
        $this->assertSame(null, $data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertSame(null, $data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testStreet(): void
    {
        $data = new BillingData();
        $this->assertSame(null, $data->getStreet());

        $data->setStreet('text');
        $this->assertSame('text', $data->getStreet());

        $data->setStreet(null);
        $this->assertSame(null, $data->getStreet());
    }

    public function testStreetValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData();
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'street');
        $this->assertEmpty($result); // valid

        $data->setStreet(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'street');
        $this->assertNotEmpty($result); // invalid
    }

    public function testTown(): void
    {
        $data = new BillingData();
        $this->assertSame(null, $data->getTown());

        $data->setTown('text');
        $this->assertSame('text', $data->getTown());

        $data->setTown(null);
        $this->assertSame(null, $data->getTown());
    }

    public function testTownValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData();
        $result = $validator->validateProperty($data, 'town');
        $this->assertEmpty($result); // valid

        $data->setTown(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'town');
        $this->assertEmpty($result); // valid

        $data->setTown(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'town');
        $this->assertNotEmpty($result); // invalid
    }

    public function testCountry(): void
    {
        $data = new BillingData();
        $this->assertSame(null, $data->getCountry());

        $data->setCountry('text');
        $this->assertSame('text', $data->getCountry());

        $data->setCountry(null);
        $this->assertSame(null, $data->getCountry());
    }

    public function testCountryValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData();
        $result = $validator->validateProperty($data, 'country');
        $this->assertEmpty($result); // valid

        $data->setCountry(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'country');
        $this->assertEmpty($result); // valid

        $data->setCountry(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'country');
        $this->assertNotEmpty($result); // invalid
    }

    public function testZip(): void
    {
        $data = new BillingData();
        $this->assertSame(null, $data->getZip());

        $data->setZip('text');
        $this->assertSame('text', $data->getZip());

        $data->setZip(null);
        $this->assertSame(null, $data->getZip());
    }

    public function testZipValidation(): void
    {
        $validator = $this->getValidator();

        $data = new BillingData();
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('12345');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 45');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 456789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123456789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid

        $data->setZip('123 45 6789');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertEmpty($result); // valid


        $data->setZip('123450');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 450');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 67890');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45 67890');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid


        $data->setZip('1234');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 4');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('12345 678');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('123 45 678');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid


        $data->setZip('xxxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxx xx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxxxx xxxx');
        $result = $validator->validateProperty($data, 'zip');
        $this->assertNotEmpty($result); // invalid

        $data->setZip('xxx xx xxxx');
        $result = $validator->validateProperty($data, 'zip');
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