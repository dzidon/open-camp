<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationSortEnum;
use PHPUnit\Framework\TestCase;

class TripLocationSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new TripLocationSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new TripLocationSearchData();
        $this->assertSame(TripLocationSortEnum::PRIORITY_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(TripLocationSortEnum::PRIORITY_DESC, $data->getSortBy());

        $data->setSortBy(TripLocationSortEnum::CREATED_AT_ASC);
        $this->assertSame(TripLocationSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}