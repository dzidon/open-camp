<?php

namespace App\Tests\Model\Entity;

use App\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CamperTest extends TestCase
{
    private const NAME = 'name';
    private const GENDER = GenderEnum::FEMALE;
    private const BORN_AT = '2023-06-10 12:00:00';

    private Camper $camper;
    private User $user;

    public function testUser(): void
    {
        $this->assertSame($this->user, $this->camper->getUser());

        $userNew = new User('bob2@gmail.com');
        $this->camper->setUser($userNew);

        $this->assertSame($userNew, $this->camper->getUser());
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->camper->getName());

        $newName = 'new_name';
        $this->camper->setName($newName);
        $this->assertSame($newName, $this->camper->getName());
    }

    public function testGender(): void
    {
        $this->assertSame(self::GENDER, $this->camper->getGender());

        $newGender = GenderEnum::MALE;
        $this->camper->setGender($newGender);
        $this->assertSame($newGender, $this->camper->getGender());
    }

    public function testBornAt(): void
    {
        $bornAt = $this->camper->getBornAt();
        $this->assertSame(self::BORN_AT, $bornAt->format('Y-m-d H:i:s'));

        $newBornAtString = '2024-07-11 13:00:00';
        $newBornAt = new DateTimeImmutable($newBornAtString);
        $this->camper->setBornAt($newBornAt);
        $bornAt = $this->camper->getBornAt();
        $this->assertSame($newBornAtString, $bornAt->format('Y-m-d H:i:s'));
    }

    public function testDietaryRestrictions(): void
    {
        $this->assertNull($this->camper->getDietaryRestrictions());

        $newDietaryRestrictions = 'text';
        $this->camper->setDietaryRestrictions($newDietaryRestrictions);
        $this->assertSame($newDietaryRestrictions, $this->camper->getDietaryRestrictions());

        $this->camper->setDietaryRestrictions(null);
        $this->assertNull($this->camper->getDietaryRestrictions());
    }

    public function testHealthRestrictions(): void
    {
        $this->assertNull($this->camper->getHealthRestrictions());

        $newHealthRestrictions = 'text';
        $this->camper->setHealthRestrictions($newHealthRestrictions);
        $this->assertSame($newHealthRestrictions, $this->camper->getHealthRestrictions());

        $this->camper->setHealthRestrictions(null);
        $this->assertNull($this->camper->getHealthRestrictions());
    }

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');

        $bornAt = new DateTimeImmutable(self::BORN_AT);
        $this->camper = new Camper(self::NAME, self::GENDER, $bornAt, $this->user);
    }
}