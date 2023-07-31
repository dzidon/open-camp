<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\CampCategory;
use DateTimeImmutable;

/**
 * Admin camp search data.
 */
interface CampSearchDataInterface
{
    public function getPhrase(): string;

    public function setPhrase(?string $phrase): self;

    public function getSortBy(): CampSortEnum;

    public function setSortBy(?CampSortEnum $sortBy): void;

    public function getAge(): ?int;

    public function setAge(?int $age): self;

    public function getDateStart(): ?DateTimeImmutable;

    public function setDateStart(?DateTimeImmutable $dateStart): self;

    public function getDateEnd(): ?DateTimeImmutable;

    public function setDateEnd(?DateTimeImmutable $dateEnd): self;

    public function getCampCategory(): ?CampCategory;

    public function setCampCategory(?CampCategory $campCategory): self;

    public function getActive(): ?bool;

    public function setActive(?bool $active): self;
}