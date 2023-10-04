<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemSortEnum;
use PHPUnit\Framework\TestCase;

class PurchasableItemSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new PurchasableItemSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new PurchasableItemSearchData();
        $this->assertSame(PurchasableItemSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(PurchasableItemSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(PurchasableItemSortEnum::CREATED_AT_ASC);
        $this->assertSame(PurchasableItemSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}