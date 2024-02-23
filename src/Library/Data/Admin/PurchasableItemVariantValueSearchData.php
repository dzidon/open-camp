<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantValueSortEnum;

class PurchasableItemVariantValueSearchData
{
    private string $phrase = '';

    private PurchasableItemVariantValueSortEnum $sortBy = PurchasableItemVariantValueSortEnum::PRIORITY_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): PurchasableItemVariantValueSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?PurchasableItemVariantValueSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = PurchasableItemVariantValueSortEnum::PRIORITY_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}