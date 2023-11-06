<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class CampTest extends TestCase
{
    private const NAME = 'Name';
    private const URL_NAME = 'url-name';
    private const AGE_MIN = 1;
    private const AGE_MAX = 2;
    private const STREET = 'Street 123';
    private const TOWN = 'Test town';
    private const ZIP = '12345';
    private const COUNTRY = 'CZ';
    private const PRIORITY = 321;

    private Camp $camp;

    public function testId(): void
    {
        $id = $this->camp->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->camp->getName());

        $newName = 'New Name';
        $this->camp->setName($newName);
        $this->assertSame($newName, $this->camp->getName());
    }

    public function testUrlName(): void
    {
        $this->assertSame(self::URL_NAME, $this->camp->getUrlName());

        $newUrlName = 'new-url-name';
        $this->camp->setUrlName($newUrlName);
        $this->assertSame($newUrlName, $this->camp->getUrlName());
    }

    public function testAgeMin(): void
    {
        $this->assertSame(self::AGE_MIN, $this->camp->getAgeMin());

        $newAgeMin = 3;
        $this->camp->setAgeMin($newAgeMin);
        $this->assertSame($newAgeMin, $this->camp->getAgeMin());
    }

    public function testAgeMax(): void
    {
        $this->assertSame(self::AGE_MAX, $this->camp->getAgeMax());

        $newAgeMax = 6;
        $this->camp->setAgeMax($newAgeMax);
        $this->assertSame($newAgeMax, $this->camp->getAgeMax());
    }

    public function testStreet(): void
    {
        $this->assertSame(self::STREET, $this->camp->getStreet());

        $newStreet = 'New street 123';
        $this->camp->setStreet($newStreet);
        $this->assertSame($newStreet, $this->camp->getStreet());
    }

    public function testTown(): void
    {
        $this->assertSame(self::TOWN, $this->camp->getTown());

        $newTown = 'New town';
        $this->camp->setTown($newTown);
        $this->assertSame($newTown, $this->camp->getTown());
    }

    public function testZip(): void
    {
        $this->assertSame(self::ZIP, $this->camp->getZip());

        $newZip = '54321';
        $this->camp->setZip($newZip);
        $this->assertSame($newZip, $this->camp->getZip());
    }

    public function testCountry(): void
    {
        $this->assertSame(self::COUNTRY, $this->camp->getCountry());

        $newCountry = 'SK';
        $this->camp->setCountry($newCountry);
        $this->assertSame($newCountry, $this->camp->getCountry());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->camp->getPriority());

        $this->camp->setPriority(123);
        $this->assertSame(123, $this->camp->getPriority());
    }

    public function testIsFeatured(): void
    {
        $this->assertFalse($this->camp->isFeatured());

        $this->camp->setIsFeatured(true);
        $this->assertTrue($this->camp->isFeatured());
    }

    public function testIsHidden(): void
    {
        $this->assertFalse($this->camp->isHidden());

        $this->camp->setIsHidden(true);
        $this->assertTrue($this->camp->isHidden());
    }

    public function testDescriptionShort(): void
    {
        $this->assertNull($this->camp->getDescriptionShort());

        $this->camp->setDescriptionShort('text');
        $this->assertSame('text', $this->camp->getDescriptionShort());

        $this->camp->setDescriptionShort(null);
        $this->assertNull($this->camp->getDescriptionShort());
    }

    public function testDescriptionLong(): void
    {
        $this->assertNull($this->camp->getDescriptionLong());

        $this->camp->setDescriptionLong('text');
        $this->assertSame('text', $this->camp->getDescriptionLong());

        $this->camp->setDescriptionLong(null);
        $this->assertNull($this->camp->getDescriptionLong());
    }

    public function testCampCategory(): void
    {
        $this->assertNull($this->camp->getCampCategory());

        $campCategory = new CampCategory('Category', 'category');
        $this->camp->setCampCategory($campCategory);

        $this->assertSame($campCategory, $this->camp->getCampCategory());

        $this->camp->setCampCategory(null);
        $this->assertNull($this->camp->getCampCategory());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->camp->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->camp->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->camp = new Camp(self::NAME, self::URL_NAME, self::AGE_MIN, self::AGE_MAX, self::STREET, self::TOWN, self::ZIP, self::COUNTRY, self::PRIORITY);
    }
}