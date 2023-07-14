<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;

/**
 * @inheritDoc
 */
class RoleSearchData implements RoleSearchDataInterface
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