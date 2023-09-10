<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\TripLocationSortEnum;

class TripLocationSearchData
{
    private string $phrase = '';

    private TripLocationSortEnum $sortBy = TripLocationSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): TripLocationSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?TripLocationSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = TripLocationSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;
    }
}