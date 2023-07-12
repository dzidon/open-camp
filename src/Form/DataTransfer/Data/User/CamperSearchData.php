<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\Search\Data\User\CamperSortEnum;

/**
 * @inheritDoc
 */
class CamperSearchData implements CamperSearchDataInterface
{
    private string $phrase = '';

    private CamperSortEnum $sortBy = CamperSortEnum::ID_DESC;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): CamperSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?CamperSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = CamperSortEnum::ID_DESC;
        }

        $this->sortBy = $sortBy;
    }
}