<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\ContactSortEnum;

/**
 * User contact search data.
 */
interface ContactSearchDataInterface
{
    public function getPhrase(): string;

    public function setPhrase(?string $phrase): self;

    public function getSortBy(): ContactSortEnum;

    public function setSortBy(?ContactSortEnum $sortBy): void;
}