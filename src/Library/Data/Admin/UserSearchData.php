<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\UserSortEnum;
use App\Model\Entity\Role;

class UserSearchData
{
    private string $phrase = '';

    private UserSortEnum $sortBy = UserSortEnum::CREATED_AT_DESC;

    private null|false|Role $role = null;

    private ?bool $isFeaturedGuide = null;

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

    public function setSortBy(?UserSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = UserSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getRole(): null|false|Role
    {
        return $this->role;
    }

    public function setRole(null|false|Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function isFeaturedGuide(): ?bool
    {
        return $this->isFeaturedGuide;
    }

    public function setIsFeaturedGuide(?bool $isFeaturedGuide): self
    {
        $this->isFeaturedGuide = $isFeaturedGuide;

        return $this;
    }
}