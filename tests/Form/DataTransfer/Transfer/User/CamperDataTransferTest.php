<?php

namespace App\Tests\Form\DataTransfer\Transfer\User;

use App\Enum\GenderEnum;
use App\Form\DataTransfer\Data\User\CamperData;
use App\Form\DataTransfer\Transfer\User\CamperDataTransfer;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class CamperDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true);

        $expectedName = 'Name';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';

        $user = new User('bob@gmail.com');
        $camper = new Camper($expectedName, $expectedGender, $expectedBornAt, $user);
        $camper->setDietaryRestrictions($expectedDietaryRestrictions);
        $camper->setHealthRestrictions($expectedHealthRestrictions);

        $data = new CamperData();
        $dataTransfer->fillData($data, $camper);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedBornAt, $data->getBornAt());
        $this->assertSame($expectedGender, $data->getGender());
        $this->assertSame($expectedDietaryRestrictions, $data->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $data->getHealthRestrictions());
    }

    public function testFillEntityWithSiblings(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true);

        $expectedName = 'Name';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';

        $user = new User('bob@gmail.com');
        $camper = new Camper('', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);
        $expectedSiblings = [
            new Camper('Camper 1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper 2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
        ];

        $data = new CamperData();
        $data->setName($expectedName);
        $data->setBornAt($expectedBornAt);
        $data->setGender($expectedGender);
        $data->setDietaryRestrictions($expectedDietaryRestrictions);
        $data->setHealthRestrictions($expectedHealthRestrictions);
        $data->setSiblings($expectedSiblings);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertSame($expectedName, $camper->getName());
        $this->assertSame($expectedBornAt, $camper->getBornAt());
        $this->assertSame($expectedGender, $camper->getGender());
        $this->assertSame($expectedDietaryRestrictions, $camper->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $camper->getHealthRestrictions());
        $this->assertSame($expectedSiblings, $camper->getSiblings());
    }

    public function testFillEntityWithoutSiblings(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(false);

        $expectedName = 'Name';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';

        $user = new User('bob@gmail.com');
        $camper = new Camper('', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);
        $expectedSiblings = [
            new Camper('Camper 1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper 2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
        ];

        $data = new CamperData();
        $data->setName($expectedName);
        $data->setBornAt($expectedBornAt);
        $data->setGender($expectedGender);
        $data->setDietaryRestrictions($expectedDietaryRestrictions);
        $data->setHealthRestrictions($expectedHealthRestrictions);
        $data->setSiblings($expectedSiblings);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertSame($expectedName, $camper->getName());
        $this->assertSame($expectedBornAt, $camper->getBornAt());
        $this->assertSame($expectedGender, $camper->getGender());
        $this->assertSame($expectedDietaryRestrictions, $camper->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $camper->getHealthRestrictions());
        $this->assertEmpty($camper->getSiblings());
    }

    private function getCamperDataTransfer(bool $isSaleCamperRecurringEnabled): CamperDataTransfer
    {
        $container = static::getContainer();

        /** @var PropertyAccessorInterface $propertyAccessor */
        $propertyAccessor = $container->get(PropertyAccessorInterface::class);

        return new CamperDataTransfer($propertyAccessor, $isSaleCamperRecurringEnabled);
    }
}