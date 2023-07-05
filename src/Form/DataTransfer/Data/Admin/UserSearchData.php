<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Entity\Role;
use App\Enum\Search\Data\Admin\UserSortEnum;
use App\Form\Type\Admin\UserSearchType;

/**
 * See {@link UserSearchType}
 */
class UserSearchData implements UserSearchDataInterface
{
    private string $email = '';

    private UserSortEnum $sortBy = UserSortEnum::ID_DESC;

    private ?Role $role = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = (string) $email;

        return $this;
    }

    public function getSortBy(): UserSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?UserSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = UserSortEnum::ID_DESC;
        }

        $this->sortBy = $sortBy;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}