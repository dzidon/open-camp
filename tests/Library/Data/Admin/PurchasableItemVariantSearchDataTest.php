<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemVariantSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantSortEnum;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new PurchasableItemVariantSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new PurchasableItemVariantSearchData();
        $this->assertSame(PurchasableItemVariantSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(PurchasableItemVariantSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(PurchasableItemVariantSortEnum::CREATED_AT_ASC);
        $this->assertSame(PurchasableItemVariantSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}