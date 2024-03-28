<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationContactSortEnum;

class ApplicationContactSearchData
{
    private string $phrase = '';

    private ApplicationContactSortEnum $sortBy = ApplicationContactSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): ApplicationContactSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationContactSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationContactSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}