<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\DataTransfer\Data\Admin\RoleSearchData;
use PHPUnit\Framework\TestCase;

class RoleSearchDataTest extends TestCase
{
    public function testLabel(): void
    {
        $data = new RoleSearchData();
        $this->assertSame('', $data->getLabel());

        $data->setLabel(null);
        $this->assertSame('', $data->getLabel());

        $data->setLabel('text');
        $this->assertSame('text', $data->getLabel());
    }

    public function testSortBy(): void
    {
        $data = new RoleSearchData();
        $this->assertSame(RoleSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(RoleSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(RoleSortEnum::ID_ASC);
        $this->assertSame(RoleSortEnum::ID_ASC, $data->getSortBy());
    }
}