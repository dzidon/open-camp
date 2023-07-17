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

    public function testSiblings(): void
    {
        $this->assertEmpty($this->camper->getSiblings());

        $sibling1Name = 'Sibling 1';
        $sibling2Name = 'Sibling 2';
        $sibling1 = new Camper($sibling1Name, GenderEnum::FEMALE, new DateTimeImmutable(), $this->user);
        $sibling2 = new Camper($sibling2Name, GenderEnum::MALE, new DateTimeImmutable(), $this->user);

        // add
        $this->camper->addSibling($sibling1);
        $this->assertSame([$sibling1Name], $this->getSiblingNames($this->camper));
        $this->assertSame([self::NAME], $this->getSiblingNames($sibling1));

        $this->camper->addSibling($sibling2);
        $this->assertSame([$sibling1Name, $sibling2Name], $this->getSiblingNames($this->camper));
        $this->assertSame([self::NAME, $sibling2Name], $this->getSiblingNames($sibling1));
        $this->assertSame([self::NAME, $sibling1Name], $this->getSiblingNames($sibling2));

        // remove
        $this->camper->removeSibling($sibling2);
        $this->assertEmpty($this->getSiblingNames($sibling2));
        $this->assertSame([$sibling1Name], $this->getSiblingNames($this->camper));
        $this->assertSame([self::NAME], $this->getSiblingNames($sibling1));

        $this->camper->removeSibling($sibling1);
        $this->assertEmpty($this->getSiblingNames($sibling2));
        $this->assertEmpty($this->getSiblingNames($this->camper));
        $this->assertEmpty($this->getSiblingNames($sibling1));
    }

    public function getSiblingNames(Camper $camper): array
    {
        $names = [];

        foreach ($camper->getSiblings() as $sibling)
        {
            $names[] = $sibling->getName();
        }

        return $names;
    }

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');

        $bornAt = new DateTimeImmutable(self::BORN_AT);
        $this->camper = new Camper(self::NAME, self::GENDER, $bornAt, $this->user);
    }
}