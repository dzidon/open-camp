<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Enum\Search\Data\User\CamperSortEnum;
use App\Form\DataTransfer\Data\User\CamperSearchData;
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
        $this->assertSame(CamperSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(CamperSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(CamperSortEnum::ID_ASC);
        $this->assertSame(CamperSortEnum::ID_ASC, $data->getSortBy());
    }
}