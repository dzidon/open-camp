<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\CampCategory;
use DateTimeImmutable;

class CampSearchData
{
    private string $phrase = '';

    private CampSortEnum $sortBy = CampSortEnum::CREATED_AT_DESC;

    private ?int $age = null;

    private ?DateTimeImmutable $startAt = null;

    private ?DateTimeImmutable $endAt = null;

    private false|null|CampCategory $campCategory = null;

    private ?bool $isActive = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): CampSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?CampSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = CampSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;
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

    public function getCampCategory(): false|null|CampCategory
    {
        return $this->campCategory;
    }

    public function setCampCategory(false|null|CampCategory $campCategory): self
    {
        $this->campCategory = $campCategory;

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