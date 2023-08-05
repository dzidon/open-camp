<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Enum\GenderEnum;
use App\Form\DataTransfer\Data\User\CamperData;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CamperDataTest extends KernelTestCase
{
    public function testIsNationalIdentifierEnabled(): void
    {
        $data = new CamperData(false);
        $this->assertFalse($data->isNationalIdentifierEnabled());

        $data = new CamperData(true);
        $this->assertTrue($data->isNationalIdentifierEnabled());
    }

    public function testNameFirst(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getNameFirst());

        $data->setNameFirst('text');
        $this->assertSame('text', $data->getNameFirst());

        $data->setNameFirst(null);
        $this->assertNull($data->getNameFirst());
    }

    public function testNameFirstValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst('');
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(null);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNameLast(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getNameLast());

        $data->setNameLast('text');
        $this->assertSame('text', $data->getNameLast());

        $data->setNameLast(null);
        $this->assertNull($data->getNameLast());
    }

    public function testNameLastValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast('');
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(null);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid
    }

    public function testGender(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getGender());

        $data->setGender(GenderEnum::MALE);
        $this->assertSame(GenderEnum::MALE, $data->getGender());

        $data->setGender(null);
        $this->assertNull($data->getGender());
    }

    public function testGenderValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertNotEmpty($result); // invalid

        $data->setGender(GenderEnum::MALE);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertEmpty($result); // valid

        $data->setGender(null);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNationalIdentifier(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getNationalIdentifier());

        $data->setNationalIdentifier('text');
        $this->assertSame('text', $data->getNationalIdentifier());

        $data->setNationalIdentifier(null);
        $this->assertNull($data->getNationalIdentifier());
    }

    public function testNationalIdentifierValidationIfEnabledAndNotAbsent(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(true);
        $data->setIsNationalIdentifierAbsent(false);

        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier(null);
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('text');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid


        $data->setNationalIdentifier('000116/0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('0001160987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('000116 09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('00011609871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid


        $data->setNationalIdentifier('000116/09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('000116 09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid

        $data->setNationalIdentifier('00011609');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNationalIdentifierValidationIfDisabledAndNotAbsent(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $data->setIsNationalIdentifierAbsent(false);

        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier(null);
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('text');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('0001160987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid
    }

    public function testNationalIdentifierValidationIfDisabledAndAbsent(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $data->setIsNationalIdentifierAbsent(true);

        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier(null);
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('text');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('0001160987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid
    }

    public function testNationalIdentifierValidationIfEnabledAndAbsent(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(true);
        $data->setIsNationalIdentifierAbsent(true);

        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier(null);
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('text');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 0987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('0001160987');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116098');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609871');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid


        $data->setNationalIdentifier('000116/09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('000116 09');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid

        $data->setNationalIdentifier('00011609');
        $result = $validator->validateProperty($data, 'nationalIdentifier');
        $this->assertEmpty($result); // valid
    }

    public function testIsNationalIdentifierAbsent(): void
    {
        $data = new CamperData(false);
        $this->assertFalse($data->isNationalIdentifierAbsent());

        $data->setIsNationalIdentifierAbsent(true);
        $this->assertTrue($data->isNationalIdentifierAbsent());

        $data->setIsNationalIdentifierAbsent(false);
        $this->assertFalse($data->isNationalIdentifierAbsent());
    }

    public function testBornAt(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getBornAt());

        $expectedBornAt = new DateTimeImmutable('now');
        $data->setBornAt($expectedBornAt);
        $this->assertSame($expectedBornAt, $data->getBornAt());

        $data->setBornAt(null);
        $this->assertNull($data->getBornAt());
    }

    public function testBornAtValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'bornAt');
        $this->assertNotEmpty($result); // invalid

        $now = new DateTimeImmutable('now');
        $data->setBornAt($now);
        $result = $validator->validateProperty($data, 'bornAt');
        $this->assertNotEmpty($result); // invalid

        $future = new DateTimeImmutable('3000-01-01 12:00:00');
        $data->setBornAt($future);
        $result = $validator->validateProperty($data, 'bornAt');
        $this->assertNotEmpty($result); // invalid

        $past = new DateTimeImmutable('2000-01-01 12:00:00');
        $data->setBornAt($past);
        $result = $validator->validateProperty($data, 'bornAt');
        $this->assertEmpty($result); // valid

        $data->setBornAt(null);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertNotEmpty($result); // invalid
    }

    public function testDietaryRestrictions(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getDietaryRestrictions());

        $data->setDietaryRestrictions('text');
        $this->assertSame('text', $data->getDietaryRestrictions());

        $data->setDietaryRestrictions(null);
        $this->assertNull($data->getDietaryRestrictions());
    }

    public function testDietaryRestrictionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'dietaryRestrictions');
        $this->assertEmpty($result); // valid

        $data->setDietaryRestrictions('');
        $result = $validator->validateProperty($data, 'dietaryRestrictions');
        $this->assertEmpty($result); // valid

        $data->setDietaryRestrictions(null);
        $result = $validator->validateProperty($data, 'dietaryRestrictions');
        $this->assertEmpty($result); // valid

        $data->setDietaryRestrictions(str_repeat('x', 1000));
        $result = $validator->validateProperty($data, 'dietaryRestrictions');
        $this->assertEmpty($result); // valid

        $data->setDietaryRestrictions(str_repeat('x', 1001));
        $result = $validator->validateProperty($data, 'dietaryRestrictions');
        $this->assertNotEmpty($result); // invalid
    }

    public function testHealthRestrictions(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getHealthRestrictions());

        $data->setHealthRestrictions('text');
        $this->assertSame('text', $data->getHealthRestrictions());

        $data->setHealthRestrictions(null);
        $this->assertNull($data->getHealthRestrictions());
    }

    public function testHealthRestrictionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'healthRestrictions');
        $this->assertEmpty($result); // valid

        $data->setHealthRestrictions('');
        $result = $validator->validateProperty($data, 'healthRestrictions');
        $this->assertEmpty($result); // valid

        $data->setHealthRestrictions(null);
        $result = $validator->validateProperty($data, 'healthRestrictions');
        $this->assertEmpty($result); // valid

        $data->setHealthRestrictions(str_repeat('x', 1000));
        $result = $validator->validateProperty($data, 'healthRestrictions');
        $this->assertEmpty($result); // valid

        $data->setHealthRestrictions(str_repeat('x', 1001));
        $result = $validator->validateProperty($data, 'healthRestrictions');
        $this->assertNotEmpty($result); // invalid
    }

    public function testMedication(): void
    {
        $data = new CamperData(false);
        $this->assertNull($data->getMedication());

        $data->setMedication('text');
        $this->assertSame('text', $data->getMedication());

        $data->setMedication(null);
        $this->assertNull($data->getMedication());
    }

    public function testMedicationValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData(false);
        $result = $validator->validateProperty($data, 'medication');
        $this->assertEmpty($result); // valid

        $data->setMedication('');
        $result = $validator->validateProperty($data, 'medication');
        $this->assertEmpty($result); // valid

        $data->setMedication(null);
        $result = $validator->validateProperty($data, 'medication');
        $this->assertEmpty($result); // valid

        $data->setMedication(str_repeat('x', 1000));
        $result = $validator->validateProperty($data, 'medication');
        $this->assertEmpty($result); // valid

        $data->setMedication(str_repeat('x', 1001));
        $result = $validator->validateProperty($data, 'medication');
        $this->assertNotEmpty($result); // invalid
    }

    public function testSiblings(): void
    {
        $data = new CamperData(false);
        $this->assertEmpty($data->getSiblings());

        $user = new User('bob@gmail.com');
        $siblings = [
            new Camper('Camper', '1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper', '2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
        ];

        $data->setSiblings($siblings);
        $this->assertSame($siblings, $data->getSiblings());
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}