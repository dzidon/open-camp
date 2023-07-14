<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\DataTransfer\Data\Admin\RoleSearchData;
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