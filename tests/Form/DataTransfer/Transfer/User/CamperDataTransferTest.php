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
        $dataTransfer = $this->getCamperDataTransfer(true, true);

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedNationalIdentifier = '1234';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';
        $expectedMedication = 'Medication...';

        $user = new User('bob@gmail.com');
        $camper = new Camper($expectedNameFirst, $expectedNameLast, $expectedGender, $expectedBornAt, $user);
        $camper->setNationalIdentifier($expectedNationalIdentifier);
        $camper->setDietaryRestrictions($expectedDietaryRestrictions);
        $camper->setHealthRestrictions($expectedHealthRestrictions);
        $camper->setMedication($expectedMedication);

        $data = new CamperData(true);
        $dataTransfer->fillData($data, $camper);

        $this->assertSame($expectedNameFirst, $data->getNameFirst());
        $this->assertSame($expectedNameLast, $data->getNameLast());
        $this->assertSame($expectedNationalIdentifier, $data->getNationalIdentifier());
        $this->assertSame($expectedBornAt, $data->getBornAt());
        $this->assertSame($expectedGender, $data->getGender());
        $this->assertSame($expectedDietaryRestrictions, $data->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $data->getHealthRestrictions());
        $this->assertSame($expectedMedication, $data->getMedication());
        $this->assertFalse($data->isNationalIdentifierAbsent());
    }

    public function testFillDataWithDisabledNationalId(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true, false);

        $user = new User('bob@gmail.com');
        $camper = new Camper('John', 'Doe', GenderEnum::MALE, new DateTimeImmutable(), $user);
        $camper->setNationalIdentifier('1234');

        $data = new CamperData(false);
        $dataTransfer->fillData($data, $camper);

        $this->assertNull($data->getNationalIdentifier());
        $this->assertFalse($data->isNationalIdentifierAbsent());
    }

    public function testFillDataWithNullNationalId(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true, true);

        $user = new User('bob@gmail.com');
        $camper = new Camper('John', 'Doe', GenderEnum::MALE, new DateTimeImmutable(), $user);

        $data = new CamperData(true);
        $dataTransfer->fillData($data, $camper);

        $this->assertNull($data->getNationalIdentifier());
        $this->assertTrue($data->isNationalIdentifierAbsent());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true, true);

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedNationalIdentifier = '1234';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';
        $expectedMedication = 'Medication...';

        $user = new User('bob@gmail.com');
        $camper = new Camper('', '', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);
        $expectedSiblings = [
            new Camper('Camper', '1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper', '2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
        ];

        $data = new CamperData(true);
        $data->setNameFirst($expectedNameFirst);
        $data->setNameLast($expectedNameLast);
        $data->setNationalIdentifier($expectedNationalIdentifier);
        $data->setBornAt($expectedBornAt);
        $data->setGender($expectedGender);
        $data->setDietaryRestrictions($expectedDietaryRestrictions);
        $data->setHealthRestrictions($expectedHealthRestrictions);
        $data->setMedication($expectedMedication);
        $data->setSiblings($expectedSiblings);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertSame($expectedNameFirst, $camper->getNameFirst());
        $this->assertSame($expectedNameLast, $camper->getNameLast());
        $this->assertSame($expectedNationalIdentifier, $camper->getNationalIdentifier());
        $this->assertSame($expectedBornAt, $camper->getBornAt());
        $this->assertSame($expectedGender, $camper->getGender());
        $this->assertSame($expectedDietaryRestrictions, $camper->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $camper->getHealthRestrictions());
        $this->assertSame($expectedMedication, $data->getMedication());
        $this->assertSame($expectedSiblings, $camper->getSiblings());
    }

    public function testFillEntityWithDisabledNationalId(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true, false);

        $user = new User('bob@gmail.com');
        $camper = new Camper('', '', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);

        $data = new CamperData(false);
        $data->setNameFirst('John');
        $data->setNameLast('Doe');
        $data->setBornAt(new DateTimeImmutable('now'));
        $data->setGender(GenderEnum::MALE);
        $data->setNationalIdentifier('1234');

        $dataTransfer->fillEntity($data, $camper);

        $this->assertNull($camper->getNationalIdentifier());
    }

    public function testFillEntityWithAbsentNationalId(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(true, true);

        $user = new User('bob@gmail.com');
        $camper = new Camper('', '', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);

        $data = new CamperData(true);
        $data->setNameFirst('John');
        $data->setNameLast('Doe');
        $data->setBornAt(new DateTimeImmutable('now'));
        $data->setGender(GenderEnum::MALE);
        $data->setNationalIdentifier('1234');
        $data->setIsNationalIdentifierAbsent(true);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertNull($camper->getNationalIdentifier());
    }

    public function testFillEntityWithoutSiblings(): void
    {
        $dataTransfer = $this->getCamperDataTransfer(false, true);

        $user = new User('bob@gmail.com');
        $camper = new Camper('', '', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);
        $expectedSiblings = [
            new Camper('Camper', '1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper', '2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
        ];

        $data = new CamperData(true);
        $data->setNameFirst('John');
        $data->setNameLast('Doe');
        $data->setBornAt(new DateTimeImmutable('now'));
        $data->setGender(GenderEnum::MALE);
        $data->setSiblings($expectedSiblings);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertEmpty($camper->getSiblings());
    }

    private function getCamperDataTransfer(bool $isSaleCamperRecurringEnabled, bool $isNationalIdentifierEnabled): CamperDataTransfer
    {
        $container = static::getContainer();

        /** @var PropertyAccessorInterface $propertyAccessor */
        $propertyAccessor = $container->get(PropertyAccessorInterface::class);

        return new CamperDataTransfer($propertyAccessor, $isSaleCamperRecurringEnabled, $isNationalIdentifierEnabled);
    }
}