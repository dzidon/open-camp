<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\Camp;
use App\Model\Entity\User;
use App\Model\Repository\CampRepositoryInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDateDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testCamp(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getCamp());

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $data->setCamp($camp);
        $this->assertSame($camp, $data->getCamp());

        $data->setCamp(null);
        $this->assertNull($data->getCamp());
    }

    public function testStartAt(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getStartAt());

        $expectedDateStart = new DateTimeImmutable('now');
        $data->setStartAt($expectedDateStart);
        $this->assertSame($expectedDateStart, $data->getStartAt());

        $data->setStartAt(null);
        $this->assertNull($data->getStartAt());
    }

    public function testStartAtValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData();
        $result = $validator->validateProperty($data, 'startAt');
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('now'));
        $result = $validator->validateProperty($data, 'startAt');
        $this->assertEmpty($result); // valid
    }

    public function testEndAt(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getEndAt());

        $expectedDateEnd = new DateTimeImmutable('now');
        $data->setEndAt($expectedDateEnd);
        $this->assertSame($expectedDateEnd, $data->getEndAt());

        $data->setEndAt(null);
        $this->assertNull($data->getEndAt());
    }

    public function testEndAtValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData();
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('now'));

        $data->setEndAt(new DateTimeImmutable('now'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertEmpty($result); // valid

        $data->setEndAt(new DateTimeImmutable('3000-01-01'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertEmpty($result); // valid

        $data->setEndAt(new DateTimeImmutable('2000-01-01'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertNotEmpty($result); // invalid
    }

    public function testPrice(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getPrice());

        $data->setPrice(100.5);
        $this->assertSame(100.5, $data->getPrice());

        $data->setPrice(null);
        $this->assertNull($data->getPrice());
    }

    public function testPriceValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData();
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

    public function testCapacity(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getCapacity());

        $data->setCapacity(100);
        $this->assertSame(100, $data->getCapacity());

        $data->setCapacity(null);
        $this->assertNull($data->getCapacity());
    }

    public function testCapacityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData();
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertNotEmpty($result); // invalid

        $data->setCapacity(0);
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertNotEmpty($result); // invalid

        $data->setCapacity(1);
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertEmpty($result); // valid
    }

    public function testIsClosed(): void
    {
        $data = new CampDateData();
        $this->assertFalse($data->isClosed());

        $data->setIsClosed(true);
        $this->assertTrue($data->isClosed());

        $data->setIsClosed(false);
        $this->assertFalse($data->isClosed());
    }

    public function testTripInstructions(): void
    {
        $data = new CampDateData();
        $this->assertNull($data->getTripInstructions());

        $data->setTripInstructions('text');
        $this->assertSame('text', $data->getTripInstructions());

        $data->setTripInstructions(null);
        $this->assertNull($data->getTripInstructions());
    }

    public function testTripInstructionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData();
        $result = $validator->validateProperty($data, 'tripInstructions');
        $this->assertEmpty($result); // valid

        $data->setTripInstructions('');
        $result = $validator->validateProperty($data, 'tripInstructions');
        $this->assertEmpty($result); // valid

        $data->setTripInstructions(null);
        $result = $validator->validateProperty($data, 'tripInstructions');
        $this->assertEmpty($result); // valid

        $data->setTripInstructions(str_repeat('x', 2000));
        $result = $validator->validateProperty($data, 'tripInstructions');
        $this->assertEmpty($result); // valid

        $data->setTripInstructions(str_repeat('x', 2001));
        $result = $validator->validateProperty($data, 'tripInstructions');
        $this->assertNotEmpty($result); // invalid
    }

    public function testLeaders(): void
    {
        $data = new CampDateData();
        $this->assertSame([], $data->getLeaders());

        $newLeaders = [
            new User('bob1@test.com'),
            new User('bob2@test.com'),
        ];

        foreach ($newLeaders as $newLeader)
        {
            $data->addLeader($newLeader);
        }

        $this->assertSame($newLeaders, $data->getLeaders());

        $data->removeLeader($newLeaders[0]);
        $this->assertNotContains($newLeaders[0], $data->getLeaders());
    }

    public function testIntervalCollision(): void
    {
        $validator = $this->getValidator();
        $campRepository = $this->getCampRepository();
        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camp = $campRepository->findOneById($uid);

        $data = new CampDateData();
        $data->setPrice(100.0);
        $data->setCapacity(5);
        $data->setCamp($camp);

        $data->setStartAt(new DateTimeImmutable('3000-01-01'));
        $data->setEndAt(new DateTimeImmutable('3000-01-02'));
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $data->setStartAt(new DateTimeImmutable('2000-06-20'));
        $data->setEndAt(new DateTimeImmutable('2000-07-03'));
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId(null);
        $data->setStartAt(new DateTimeImmutable('2000-06-20'));
        $data->setEndAt(new DateTimeImmutable('2000-07-03'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-06'));
        $data->setEndAt(new DateTimeImmutable('2000-07-08'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-06-30'));
        $data->setEndAt(new DateTimeImmutable('2000-07-08'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-02'));
        $data->setEndAt(new DateTimeImmutable('2000-07-06'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-06-28'));
        $data->setEndAt(new DateTimeImmutable('2000-07-01'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-07'));
        $data->setEndAt(new DateTimeImmutable('2000-07-10'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-01'));
        $data->setEndAt(new DateTimeImmutable('2000-07-07'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    private function getCampRepository(): CampRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampRepositoryInterface $repository */
        $repository = $container->get(CampRepositoryInterface::class);

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