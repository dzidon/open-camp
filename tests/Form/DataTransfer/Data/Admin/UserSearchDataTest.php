<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Entity\Role;
use App\Enum\Search\Data\Admin\UserSortEnum;
use App\Form\DataTransfer\Data\Admin\UserSearchData;
use PHPUnit\Framework\TestCase;

class UserSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new UserSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new UserSearchData();
        $this->assertSame(UserSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(UserSortEnum::ID_DESC, $data->getSortBy());

        $data->setSortBy(UserSortEnum::ID_ASC);
        $this->assertSame(UserSortEnum::ID_ASC, $data->getSortBy());
    }

    public function testRole(): void
    {
        $data = new UserSearchData();
        $this->assertSame(null, $data->getRole());

        $role = new Role('label');
        $data->setRole($role);
        $this->assertSame($role, $data->getRole());

        $data->setRole(null);
        $this->assertSame(null, $data->getRole());
    }
}