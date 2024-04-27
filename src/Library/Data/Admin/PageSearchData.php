<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\PageSortEnum;

class PageSearchData
{
    private string $phrase = '';

    private PageSortEnum $sortBy = PageSortEnum::CREATED_AT_DESC;

    private ?bool $isHidden = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): PageSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?PageSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = PageSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(?bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }
}