<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\TripLocationPathSortEnum;

class TripLocationPathSearchData
{
    private string $phrase = '';

    private TripLocationPathSortEnum $sortBy = TripLocationPathSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): TripLocationPathSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?TripLocationPathSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = TripLocationPathSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}