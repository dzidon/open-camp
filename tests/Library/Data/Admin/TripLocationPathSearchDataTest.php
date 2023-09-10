<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationPathSortEnum;
use PHPUnit\Framework\TestCase;

class TripLocationPathSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new TripLocationPathSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new TripLocationPathSearchData();
        $this->assertSame(TripLocationPathSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(TripLocationPathSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(TripLocationPathSortEnum::CREATED_AT_ASC);
        $this->assertSame(TripLocationPathSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}