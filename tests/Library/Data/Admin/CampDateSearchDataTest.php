<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CampDateSearchDataTest extends TestCase
{
    public function testFrom(): void
    {
        $data = new CampDateSearchData();
        $this->assertNull($data->getFrom());

        $expectedDateFrom = new DateTimeImmutable('now');
        $data->setFrom($expectedDateFrom);
        $this->assertSame($expectedDateFrom, $data->getFrom());

        $data->setFrom(null);
        $this->assertNull($data->getFrom());
    }

    public function testTo(): void
    {
        $data = new CampDateSearchData();
        $this->assertNull($data->getTo());

        $expectedDateTo = new DateTimeImmutable('now');
        $data->setTo($expectedDateTo);
        $this->assertSame($expectedDateTo, $data->getTo());

        $data->setTo(null);
        $this->assertNull($data->getTo());
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