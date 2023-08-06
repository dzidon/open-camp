<?php

namespace App\Tests\Library\Data\User;

use App\Library\Data\User\ContactSearchData;
use App\Library\Enum\Search\Data\User\ContactSortEnum;
use PHPUnit\Framework\TestCase;

class ContactSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new ContactSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new ContactSearchData();
        $this->assertSame(ContactSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(ContactSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(ContactSortEnum::CREATED_AT_ASC);
        $this->assertSame(ContactSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}