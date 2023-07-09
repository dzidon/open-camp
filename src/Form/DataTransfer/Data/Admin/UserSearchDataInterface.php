<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\UserSortEnum;
use App\Model\Entity\Role;

/**
 * Admin user search data.
 */
interface UserSearchDataInterface
{
    public function getPhrase(): string;

    public function setPhrase(?string $phrase): self;

    public function getSortBy(): UserSortEnum;

    public function setSortBy(?UserSortEnum $sortBy): void;

    public function getRole(): ?Role;

    public function setRole(?Role $role): self;
}