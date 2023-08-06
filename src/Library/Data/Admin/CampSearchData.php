<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\CampCategory;
use DateTimeImmutable;

/**
 * @inheritDoc
 */
class CampSearchData implements CampSearchDataInterface
{
    private string $phrase = '';

    private CampSortEnum $sortBy = CampSortEnum::CREATED_AT_DESC;

    private ?int $age = null;

    private ?DateTimeImmutable $dateStart = null;

    private ?DateTimeImmutable $dateEnd = null;

    private ?CampCategory $campCategory = null;

    private ?bool $active = null;

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

    public function getDateStart(): ?DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(?DateTimeImmutable $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTimeImmutable $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getCampCategory(): ?CampCategory
    {
        return $this->campCategory;
    }

    public function setCampCategory(?CampCategory $campCategory): self
    {
        $this->campCategory = $campCategory;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}