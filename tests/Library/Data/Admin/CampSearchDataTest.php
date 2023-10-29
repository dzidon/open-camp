<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampSearchData;
use App\Library\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\CampCategory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new CampSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new CampSearchData();
        $this->assertSame(CampSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(CampSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(CampSortEnum::CREATED_AT_ASC);
        $this->assertSame(CampSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }

    public function testAge(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getAge());

        $data->setAge(2);
        $this->assertSame(2, $data->getAge());

        $data->setAge(null);
        $this->assertNull($data->getAge());
    }

    public function testFrom(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getFrom());

        $expectedDateFrom = new DateTimeImmutable('now');
        $data->setFrom($expectedDateFrom);
        $this->assertSame($expectedDateFrom, $data->getFrom());

        $data->setFrom(null);
        $this->assertNull($data->getFrom());
    }

    public function testTo(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getTo());

        $expectedDateTo = new DateTimeImmutable('now');
        $data->setTo($expectedDateTo);
        $this->assertSame($expectedDateTo, $data->getTo());

        $data->setTo(null);
        $this->assertNull($data->getTo());
    }

    public function testCampCategory(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getCampCategory());

        $campCategory = new CampCategory('Name', 'name');
        $data->setCampCategory($campCategory);
        $this->assertSame($campCategory, $data->getCampCategory());

        $data->setCampCategory(null);
        $this->assertNull($data->getCampCategory());

        $data->setCampCategory(false);
        $this->assertFalse($data->getCampCategory());
    }

    public function testActive(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->isActive());

        $data->setIsActive(true);
        $this->assertTrue($data->isActive());

        $data->setIsActive(false);
        $this->assertFalse($data->isActive());

        $data->setIsActive(null);
        $this->assertNull($data->isActive());
    }

    public function testFeatured(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->isFeatured());

        $data->setIsFeatured(true);
        $this->assertTrue($data->isFeatured());

        $data->setIsFeatured(false);
        $this->assertFalse($data->isFeatured());

        $data->setIsFeatured(null);
        $this->assertNull($data->isFeatured());
    }
}