<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\DiscountConfigSortEnum;

class DiscountConfigSearchData
{
    private string $phrase = '';

    private DiscountConfigSortEnum $sortBy = DiscountConfigSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): DiscountConfigSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?DiscountConfigSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = DiscountConfigSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}