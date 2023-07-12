<?php

namespace App\Tests\Form\DataTransfer\Transfer\User;

use App\Enum\GenderEnum;
use App\Form\DataTransfer\Data\User\CamperData;
use App\Form\DataTransfer\Transfer\User\CamperDataTransfer;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CamperDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCamperDataTransfer();

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

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCamperDataTransfer();

        $expectedName = 'Name';
        $expectedBornAt = new DateTimeImmutable('now');
        $expectedGender = GenderEnum::FEMALE;
        $expectedDietaryRestrictions = 'Dietary...';
        $expectedHealthRestrictions = 'Health...';

        $user = new User('bob@gmail.com');
        $camper = new Camper('', GenderEnum::MALE, new DateTimeImmutable('2000-01-01 12:00:00'), $user);

        $data = new CamperData();
        $data->setName($expectedName);
        $data->setBornAt($expectedBornAt);
        $data->setGender($expectedGender);
        $data->setDietaryRestrictions($expectedDietaryRestrictions);
        $data->setHealthRestrictions($expectedHealthRestrictions);

        $dataTransfer->fillEntity($data, $camper);

        $this->assertSame($expectedName, $camper->getName());
        $this->assertSame($expectedBornAt, $camper->getBornAt());
        $this->assertSame($expectedGender, $camper->getGender());
        $this->assertSame($expectedDietaryRestrictions, $camper->getDietaryRestrictions());
        $this->assertSame($expectedHealthRestrictions, $camper->getHealthRestrictions());
    }

    private function getCamperDataTransfer(): CamperDataTransfer
    {
        $container = static::getContainer();

        /** @var CamperDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CamperDataTransfer::class);

        return $dataTransfer;
    }
}