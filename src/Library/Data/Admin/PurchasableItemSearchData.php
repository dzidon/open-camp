<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\PurchasableItemSortEnum;

class PurchasableItemSearchData
{
    private string $phrase = '';

    private PurchasableItemSortEnum $sortBy = PurchasableItemSortEnum::CREATED_AT_DESC;

    private ?bool $isGlobal = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): PurchasableItemSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?PurchasableItemSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = PurchasableItemSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function isGlobal(): ?bool
    {
        return $this->isGlobal;
    }

    public function setIsGlobal(?bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;

        return $this;
    }
}