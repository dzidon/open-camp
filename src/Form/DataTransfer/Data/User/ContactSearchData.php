<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\Search\Data\User\ContactSortEnum;

/**
 * @inheritDoc
 */
class ContactSearchData implements ContactSearchDataInterface
{
    private string $phrase = '';

    private ContactSortEnum $sortBy = ContactSortEnum::ID_DESC;

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

    public function setSortBy(?ContactSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = ContactSortEnum::ID_DESC;
        }

        $this->sortBy = $sortBy;
    }
}