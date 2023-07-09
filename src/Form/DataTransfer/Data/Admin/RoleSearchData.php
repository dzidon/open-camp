<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\Type\Admin\RoleSearchType;

/**
 * See {@link RoleSearchType}
 */
class RoleSearchData implements RoleSearchDataInterface
{
    private string $phrase = '';

    private RoleSortEnum $sortBy = RoleSortEnum::ID_DESC;

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
            $sortBy = RoleSortEnum::ID_DESC;
        }

        $this->sortBy = $sortBy;
    }
}