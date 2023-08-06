<?php

namespace App\Tests\Library\Data\User;

use App\Library\Data\User\CamperSearchData;
use App\Library\Enum\Search\Data\User\CamperSortEnum;
use PHPUnit\Framework\TestCase;

class CamperSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new CamperSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new CamperSearchData();
        $this->assertSame(CamperSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(CamperSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(CamperSortEnum::CREATED_AT_ASC);
        $this->assertSame(CamperSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}