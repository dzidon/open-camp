<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\CampSortEnum;
use DateTimeImmutable;

class CampSearchData
{
    private CampSortEnum $sortBy = CampSortEnum::PRIORITY_DESC;

    private string $phrase = '';

    private ?int $age = null;

    private ?DateTimeImmutable $from = null;

    private ?DateTimeImmutable $to = null;

    private bool $isOpenOnly = false;

    public function getSortBy(): CampSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?CampSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = CampSortEnum::PRIORITY_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

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

    public function isOpenOnly(): bool
    {
        return $this->isOpenOnly;
    }

    public function setIsOpenOnly(bool $isOpenOnly): self
    {
        $this->isOpenOnly = $isOpenOnly;

        return $this;
    }
}