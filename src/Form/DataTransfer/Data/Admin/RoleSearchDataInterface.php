<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\RoleSortEnum;

/**
 * Admin role search data.
 */
interface RoleSearchDataInterface
{
    public function getLabel(): string;

    public function setLabel(?string $label): self;

    public function getSortBy(): RoleSortEnum;

    public function setSortBy(?RoleSortEnum $sortBy): void;
}