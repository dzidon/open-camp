<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationCampDateSortEnum;
use DateTimeImmutable;

class ApplicationCampDateSearchData
{
    private ?DateTimeImmutable $from = null;

    private ?DateTimeImmutable $to = null;

    private ApplicationCampDateSortEnum $sortBy = ApplicationCampDateSortEnum::CAMP_DATE_START_AT_DESC;

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

    public function getSortBy(): ApplicationCampDateSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationCampDateSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationCampDateSortEnum::CAMP_DATE_START_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}