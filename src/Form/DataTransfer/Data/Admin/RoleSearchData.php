<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;
use App\Form\Type\Admin\RoleSearchType;

/**
 * See {@link RoleSearchType}
 */
class RoleSearchData implements RoleSearchDataInterface
{
    private string $label = '';

    private RoleSortEnum $sortBy = RoleSortEnum::ID_DESC;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = (string) $label;

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