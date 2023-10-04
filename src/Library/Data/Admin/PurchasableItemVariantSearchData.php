<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantSortEnum;

class PurchasableItemVariantSearchData
{
    private string $phrase = '';

    private PurchasableItemVariantSortEnum $sortBy = PurchasableItemVariantSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): PurchasableItemVariantSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?PurchasableItemVariantSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = PurchasableItemVariantSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;
    }
}