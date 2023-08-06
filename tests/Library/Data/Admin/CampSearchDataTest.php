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

    public function testDateStart(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getDateStart());

        $expectedDateStart = new DateTimeImmutable('now');
        $data->setDateStart($expectedDateStart);
        $this->assertSame($expectedDateStart, $data->getDateStart());

        $data->setDateStart(null);
        $this->assertNull($data->getDateStart());
    }

    public function testDateEnd(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getDateEnd());

        $expectedDateEnd = new DateTimeImmutable('now');
        $data->setDateEnd($expectedDateEnd);
        $this->assertSame($expectedDateEnd, $data->getDateEnd());

        $data->setDateEnd(null);
        $this->assertNull($data->getDateEnd());
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
    }

    public function testActive(): void
    {
        $data = new CampSearchData();
        $this->assertNull($data->getActive());

        $data->setActive(true);
        $this->assertTrue($data->getActive());

        $data->setActive(null);
        $this->assertNull($data->getActive());
    }
}