<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\CamperSortEnum;

class CamperSearchData
{
    private string $phrase = '';

    private CamperSortEnum $sortBy = CamperSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): CamperSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?CamperSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = CamperSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}