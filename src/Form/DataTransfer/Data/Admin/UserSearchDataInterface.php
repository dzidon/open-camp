<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Entity\Role;
use App\Enum\Search\Data\Admin\UserSortEnum;

/**
 * Admin user search data.
 */
interface UserSearchDataInterface
{
    public function getEmail(): string;

    public function setEmail(?string $email): self;

    public function getSortBy(): UserSortEnum;

    public function setSortBy(?UserSortEnum $sortBy): void;

    public function getRole(): ?Role;

    public function setRole(?Role $role): self;
}