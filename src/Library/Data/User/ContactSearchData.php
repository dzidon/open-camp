<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\ContactSortEnum;

class ContactSearchData
{
    private string $phrase = '';

    private ContactSortEnum $sortBy = ContactSortEnum::CREATED_AT_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): ContactSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ContactSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ContactSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}