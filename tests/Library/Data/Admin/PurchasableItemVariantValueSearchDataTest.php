<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantValueSortEnum;
use PHPUnit\Framework\TestCase;

class PurchasableItemVariantValueSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new PurchasableItemVariantValueSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new PurchasableItemVariantValueSearchData();
        $this->assertSame(PurchasableItemVariantValueSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(PurchasableItemVariantValueSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(PurchasableItemVariantValueSortEnum::CREATED_AT_ASC);
        $this->assertSame(PurchasableItemVariantValueSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }
}