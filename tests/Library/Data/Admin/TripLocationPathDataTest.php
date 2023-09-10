<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TripLocationPathDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new TripLocationPathData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testName(): void
    {
        $data = new TripLocationPathData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new TripLocationPathData();
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

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new TripLocationPathData();
        $data->setId(null);
        $data->setName('Path 1');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setId(null);
        $data->setName('Path 100');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $path = $tripLocationPathRepository->findOneByName('Path 1');
        $data->setId($path->getId());
        $data->setName('Path 1');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId($path->getId());
        $data->setName('Path 2');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    private function getTripLocationPathRepository(): TripLocationPathRepositoryInterface
    {
        $container = static::getContainer();

        /** @var TripLocationPathRepositoryInterface $repository */
        $repository = $container->get(TripLocationPathRepositoryInterface::class);

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