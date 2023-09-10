<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TripLocationDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new TripLocationData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testTripLocationPath(): void
    {
        $data = new TripLocationData();
        $this->assertNull($data->getTripLocationPath());

        $tripLocationPath = new TripLocationPath('Path...');
        $data->setTripLocationPath($tripLocationPath);
        $this->assertSame($tripLocationPath, $data->getTripLocationPath());

        $data->setTripLocationPath(null);
        $this->assertNull($data->getTripLocationPath());
    }

    public function testName(): void
    {
        $data = new TripLocationData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new TripLocationData();
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

    public function testPrice(): void
    {
        $data = new TripLocationData();
        $this->assertNull($data->getPrice());

        $data->setPrice(100.5);
        $this->assertSame(100.5, $data->getPrice());

        $data->setPrice(null);
        $this->assertNull($data->getPrice());
    }

    public function testPriceValidation(): void
    {
        $validator = $this->getValidator();

        $data = new TripLocationData();
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

    public function testPriority(): void
    {
        $data = new TripLocationData();
        $this->assertSame(0, $data->getPriority());

        $data->setPriority(100);
        $this->assertSame(100, $data->getPriority());
    }

    public function testPriorityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new TripLocationData();
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(100);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertEmpty($result); // valid

        $data->setPriority(null);
        $result = $validator->validateProperty($data, 'priority');
        $this->assertNotEmpty($result); // invalid
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $tripLocationRepository = $this->getTripLocationRepository();
        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $tripLocation = $tripLocationRepository->findOneById($uid);

        $data = new TripLocationData();
        $data->setPrice(1000.0);
        $data->setPriority(100);

        // editing (no change of name)
        $data->setTripLocationPath($tripLocation->getTripLocationPath());
        $data->setName('Location 1');
        $data->setId($tripLocation->getId());
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        // new (existing name)
        $data->setTripLocationPath($tripLocation->getTripLocationPath());
        $data->setName('Location 1');
        $data->setId(null);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        // editing (existing name)
        $data->setTripLocationPath($tripLocation->getTripLocationPath());
        $data->setName('Location 2');
        $data->setId($tripLocation->getId());
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        // editing (new name)
        $data->setTripLocationPath($tripLocation->getTripLocationPath());
        $data->setName('Location 3');
        $data->setId($tripLocation->getId());
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    private function getTripLocationRepository(): TripLocationRepositoryInterface
    {
        $container = static::getContainer();

        /** @var TripLocationRepositoryInterface $repository */
        $repository = $container->get(TripLocationRepositoryInterface::class);

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