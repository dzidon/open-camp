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
        $this->camp = new Camp(self::NAME, self::URL_NAME, self::AGE_MIN, self::AGE_MAX);
    }
}