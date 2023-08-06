<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\RoleSortEnum;

/**
 * Admin role search data.
 */
interface RoleSearchDataInterface
{
    public function getPhrase(): string;

    public function setPhrase(?string $phrase): self;

    public function getSortBy(): RoleSortEnum;

    public function setSortBy(?RoleSortEnum $sortBy): void;
}