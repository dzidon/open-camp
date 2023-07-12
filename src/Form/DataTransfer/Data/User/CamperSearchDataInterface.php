<?php

namespace App\Form\DataTransfer\Data\User;

use App\Enum\Search\Data\User\CamperSortEnum;

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