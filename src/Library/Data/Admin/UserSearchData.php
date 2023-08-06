<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\UserSortEnum;
use App\Model\Entity\Role;

/**
 * @inheritDoc
 */
class UserSearchData implements UserSearchDataInterface
{
    private string $phrase = '';

    private UserSortEnum $sortBy = UserSortEnum::CREATED_AT_DESC;

    private ?Role $role = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

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
            $sortBy = UserSortEnum::CREATED_AT_DESC;
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