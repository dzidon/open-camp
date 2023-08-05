<?php

namespace App\Tests\Model\Entity;

use App\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CamperTest extends TestCase
{
    private const NAME_FIRST = 'John';
    private const NAME_LAST = 'Doe';
    private const GENDER = GenderEnum::FEMALE;
    private const BORN_AT = '2023-06-10 12:00:00';

    private Camper $camper;
    private User $user;

    public function testId(): void
    {
        $id = $this->camper->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testUser(): void
    {
        $this->assertSame($this->user, $this->camper->getUser());

        $userNew = new User('bob2@gmail.com');
        $this->camper->setUser($userNew);

        $this->assertSame($userNew, $this->camper->getUser());
    }

    public function testNameFirst(): void
    {
        $this->assertSame(self::NAME_FIRST, $this->camper->getNameFirst());

        $newName = 'Dave';
        $this->camper->setNameFirst($newName);
        $this->assertSame($newName, $this->camper->getNameFirst());
    }

    public function testNameLast(): void
    {
        $this->assertSame(self::NAME_LAST, $this->camper->getNameLast());

        $newName = 'Smith';
        $this->camper->setNameLast($newName);
        $this->assertSame($newName, $this->camper->getNameLast());
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

    public function testNationalIdentifier(): void
    {
        $this->assertNull($this->camper->getNationalIdentifier());

        $newNationalIdentifier = '1234';
        $this->camper->setNationalIdentifier($newNationalIdentifier);
        $this->assertSame($newNationalIdentifier, $this->camper->getNationalIdentifier());

        $this->camper->setNationalIdentifier(null);
        $this->assertNull($this->camper->getNationalIdentifier());
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

    public function testMedication(): void
    {
        $this->assertNull($this->camper->getMedication());

        $newMedication = 'text';
        $this->camper->setMedication($newMedication);
        $this->assertSame($newMedication, $this->camper->getMedication());

        $this->camper->setMedication(null);
        $this->assertNull($this->camper->getMedication());
    }

    public function testSiblings(): void
    {
        $this->assertEmpty($this->camper->getSiblings());

        $sibling1 = new Camper('Sibling', '1', GenderEnum::FEMALE, new DateTimeImmutable(), $this->user);
        $sibling2 = new Camper('Sibling', '2', GenderEnum::MALE, new DateTimeImmutable(), $this->user);

        // add
        $this->camper->addSibling($sibling1);
        $this->assertSame(['Sibling 1'], $this->getSiblingNames($this->camper));
        $this->assertSame(['John Doe'], $this->getSiblingNames($sibling1));

        $this->camper->addSibling($sibling2);
        $this->assertSame(['Sibling 1', 'Sibling 2'], $this->getSiblingNames($this->camper));
        $this->assertSame(['John Doe', 'Sibling 2'], $this->getSiblingNames($sibling1));
        $this->assertSame(['John Doe', 'Sibling 1'], $this->getSiblingNames($sibling2));

        // remove
        $this->camper->removeSibling($sibling2);
        $this->assertEmpty($this->getSiblingNames($sibling2));
        $this->assertSame(['Sibling 1'], $this->getSiblingNames($this->camper));
        $this->assertSame(['John Doe'], $this->getSiblingNames($sibling1));

        $this->camper->removeSibling($sibling1);
        $this->assertEmpty($this->getSiblingNames($sibling2));
        $this->assertEmpty($this->getSiblingNames($this->camper));
        $this->assertEmpty($this->getSiblingNames($sibling1));
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->camper->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->camper->getUpdatedAt());
    }

    public function getSiblingNames(Camper $camper): array
    {
        $names = [];

        foreach ($camper->getSiblings() as $sibling)
        {
            $names[] = $sibling->getNameFirst() . ' ' . $sibling->getNameLast();
        }

        return $names;
    }

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');

        $bornAt = new DateTimeImmutable(self::BORN_AT);
        $this->camper = new Camper(self::NAME_FIRST, self::NAME_LAST, self::GENDER, $bornAt, $this->user);
    }
}