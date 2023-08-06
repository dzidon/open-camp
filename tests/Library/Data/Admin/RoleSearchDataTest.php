<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\RoleSearchData;
use App\Library\Enum\Search\Data\Admin\RoleSortEnum;
use PHPUnit\Framework\TestCase;

class RoleSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new RoleSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new RoleSearchData();
        $this->assertSame(RoleSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(RoleSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(RoleSortEnum::CREATED_AT_ASC);
        $this->assertSame(RoleSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}