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

    private ?DateTimeImmutable $from = null;

    private ?DateTimeImmutable $to = null;

    private false|null|CampCategory $campCategory = null;

    private ?bool $isOpenOnly = null;

    private ?bool $isFeatured = null;

    private ?bool $isHidden = null;

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

    public function setSortBy(?CampSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = CampSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

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

    public function getCampCategory(): false|null|CampCategory
    {
        return $this->campCategory;
    }

    public function setCampCategory(false|null|CampCategory $campCategory): self
    {
        $this->campCategory = $campCategory;

        return $this;
    }

    public function isOpenOnly(): ?bool
    {
        return $this->isOpenOnly;
    }

    public function setIsOpenOnly(?bool $isOpenOnly): self
    {
        $this->isOpenOnly = $isOpenOnly;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(?bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(?bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }
}