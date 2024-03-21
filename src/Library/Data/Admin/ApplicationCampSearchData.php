<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationCampSortEnum;

class ApplicationCampSearchData
{
    private string $phrase = '';

    private ApplicationCampSortEnum $sortBy = ApplicationCampSortEnum::NUMBER_OF_PENDING_APPLICATIONS_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): ApplicationCampSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationCampSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationCampSortEnum::NUMBER_OF_PENDING_APPLICATIONS_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}