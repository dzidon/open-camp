<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampDateSearchDataTest extends TestCase
{
    public function testStartAt(): void
    {
        $data = new CampDateSearchData();
        $this->assertNull($data->getStartAt());

        $expectedDateStart = new DateTimeImmutable('now');
        $data->setStartAt($expectedDateStart);
        $this->assertSame($expectedDateStart, $data->getStartAt());

        $data->setStartAt(null);
        $this->assertNull($data->getStartAt());
    }

    public function testEndAt(): void
    {
        $data = new CampDateSearchData();
        $this->assertNull($data->getEndAt());

        $expectedDateEnd = new DateTimeImmutable('now');
        $data->setEndAt($expectedDateEnd);
        $this->assertSame($expectedDateEnd, $data->getEndAt());

        $data->setEndAt(null);
        $this->assertNull($data->getEndAt());
    }

    public function testSortBy(): void
    {
        $data = new CampDateSearchData();
        $this->assertSame(CampDateSortEnum::START_AT_ASC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(CampDateSortEnum::START_AT_ASC, $data->getSortBy());

        $data->setSortBy(CampDateSortEnum::START_AT_DESC);
        $this->assertSame(CampDateSortEnum::START_AT_DESC, $data->getSortBy());
    }

    public function testIsHistorical(): void
    {
        $data = new CampDateSearchData();
        $this->assertFalse($data->isHistorical());

        $data->setIsHistorical(true);
        $this->assertTrue($data->isHistorical());

        $data->setIsHistorical(false);
        $this->assertFalse($data->isHistorical());

        $data->setIsHistorical(null);
        $this->assertNull($data->isHistorical());
    }

    public function testIsActive(): void
    {
        $data = new CampDateSearchData();
        $this->assertNull($data->isActive());

        $data->setIsActive(true);
        $this->assertTrue($data->isActive());

        $data->setIsActive(false);
        $this->assertFalse($data->isActive());

        $data->setIsActive(null);
        $this->assertNull($data->isActive());
    }
}