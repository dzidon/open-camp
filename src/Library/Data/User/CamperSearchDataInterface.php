<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\CamperSortEnum;

/**
 * User camper search data.
 */
interface CamperSearchDataInterface
{
    public function getPhrase(): string;

    public function setPhrase(?string $phrase): self;

    public function getSortBy(): CamperSortEnum;

    public function setSortBy(?CamperSortEnum $sortBy): void;
}