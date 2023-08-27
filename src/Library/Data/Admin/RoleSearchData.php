<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\RoleSortEnum;

class RoleSearchData
{
    private string $phrase = '';

    private RoleSortEnum $sortBy = RoleSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): RoleSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?RoleSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = RoleSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;
    }
}