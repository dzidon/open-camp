<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use DateTimeImmutable;

class CampDateSearchData
{
    private ?DateTimeImmutable $from = null;

    private ?DateTimeImmutable $to = null;

    private CampDateSortEnum $sortBy = CampDateSortEnum::START_AT_ASC;

    private ?bool $isHistorical = false;

    private ?bool $isActive = null;

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function setFrom(?DateTimeImmutable $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function setTo(?DateTimeImmutable $to): self
    {
        $this->to = $to;

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