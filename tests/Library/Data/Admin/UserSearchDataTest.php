<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\UserSearchData;
use App\Library\Enum\Search\Data\Admin\UserSortEnum;
use App\Model\Entity\Role;
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
        $this->assertSame(UserSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(UserSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(UserSortEnum::CREATED_AT_ASC);
        $this->assertSame(UserSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }

    public function testRole(): void
    {
        $data = new UserSearchData();
        $this->assertNull($data->getRole());

        $role = new Role('label');
        $data->setRole($role);
        $this->assertSame($role, $data->getRole());

        $data->setRole(null);
        $this->assertNull($data->getRole());

        $data->setRole(false);
        $this->assertFalse($data->getRole());
    }
}