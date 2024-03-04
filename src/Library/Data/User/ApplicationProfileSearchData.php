<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\ApplicationProfileSortEnum;

class ApplicationProfileSearchData
{
    private string $phrase = '';

    private ApplicationProfileSortEnum $sortBy = ApplicationProfileSortEnum::COMPLETED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): ApplicationProfileSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationProfileSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationProfileSortEnum::COMPLETED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}