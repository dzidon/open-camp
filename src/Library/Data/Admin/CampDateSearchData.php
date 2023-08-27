<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use DateTimeImmutable;

class CampDateSearchData
{
    private ?DateTimeImmutable $startAt = null;

    private ?DateTimeImmutable $endAt = null;

    private CampDateSortEnum $sortBy = CampDateSortEnum::START_AT_ASC;

    private ?bool $isHistorical = false;

    private ?bool $isActive = null;

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getSortBy(): CampDateSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?CampDateSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = CampDateSortEnum::START_AT_ASC;
        }

        $this->sortBy = $sortBy;
    }

    public function isHistorical(): ?bool
    {
        return $this->isHistorical;
    }

    public function setIsHistorical(?bool $isHistorical): self
    {
        $this->isHistorical = $isHistorical;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}