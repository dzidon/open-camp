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
    public function testName(): void
    {
        $data = new CamperData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData();
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

    public function testGender(): void
    {
        $data = new CamperData();
        $this->assertNull($data->getGender());

        $data->setGender(GenderEnum::MALE);
        $this->assertSame(GenderEnum::MALE, $data->getGender());

        $data->setGender(null);
        $this->assertNull($data->getGender());
    }

    public function testGenderValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData();
        $result = $validator->validateProperty($data, 'gender');
        $this->assertNotEmpty($result); // invalid

        $data->setGender(GenderEnum::MALE);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertEmpty($result); // valid

        $data->setGender(null);
        $result = $validator->validateProperty($data, 'gender');
        $this->assertNotEmpty($result); // invalid
    }

    public function testBornAt(): void
    {
        $data = new CamperData();
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

        $data = new CamperData();
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
        $data = new CamperData();
        $this->assertNull($data->getDietaryRestrictions());

        $data->setDietaryRestrictions('text');
        $this->assertSame('text', $data->getDietaryRestrictions());

        $data->setDietaryRestrictions(null);
        $this->assertNull($data->getDietaryRestrictions());
    }

    public function testDietaryRestrictionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData();
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
        $data = new CamperData();
        $this->assertNull($data->getHealthRestrictions());

        $data->setHealthRestrictions('text');
        $this->assertSame('text', $data->getHealthRestrictions());

        $data->setHealthRestrictions(null);
        $this->assertNull($data->getHealthRestrictions());
    }

    public function testHealthRestrictionsValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CamperData();
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

    public function testSiblings(): void
    {
        $data = new CamperData();
        $this->assertEmpty($data->getSiblings());

        $user = new User('bob@gmail.com');
        $siblings = [
            new Camper('Camper 1', GenderEnum::MALE, new DateTimeImmutable(), $user),
            new Camper('Camper 2', GenderEnum::FEMALE, new DateTimeImmutable(), $user),
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