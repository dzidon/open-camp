<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Enum\Search\Data\User\ContactSortEnum;
use App\Form\DataTransfer\Data\User\ContactSearchData;
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
        $this->assertSame(ContactSortEnum::NAME_ASC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(ContactSortEnum::NAME_ASC, $data->getSortBy());

        $data->setSortBy(ContactSortEnum::NAME_DESC);
        $this->assertSame(ContactSortEnum::NAME_DESC, $data->getSortBy());
    }
}