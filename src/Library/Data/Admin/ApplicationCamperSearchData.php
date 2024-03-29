<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\GenderEnum;
use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use App\Library\Enum\Search\Data\Admin\ApplicationCamperSortEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationCamperSearchData
{
    private string $phrase = '';

    private ApplicationCamperSortEnum $sortBy = ApplicationCamperSortEnum::CREATED_AT_DESC;

    #[Assert\GreaterThanOrEqual(0)]
    private ?int $ageMin = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'ageMin')]
    private ?int $ageMax = null;

    private ?GenderEnum $gender = null;

    private ?ApplicationAcceptedStateEnum $isApplicationAccepted = null;

    private bool $isEnabledApplicationAcceptedSearch;

    public function __construct(bool $isEnabledApplicationAcceptedSearch)
    {
        $this->isEnabledApplicationAcceptedSearch = $isEnabledApplicationAcceptedSearch;
    }

    public function isEnabledApplicationAcceptedSearch(): bool
    {
        return $this->isEnabledApplicationAcceptedSearch;
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

    public function getSortBy(): ApplicationCamperSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationCamperSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationCamperSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(?int $ageMin): self
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->ageMax;
    }

    public function setAgeMax(?int $ageMax): self
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function setGender(?GenderEnum $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getIsApplicationAccepted(): ?ApplicationAcceptedStateEnum
    {
        return $this->isApplicationAccepted;
    }

    public function setIsApplicationAccepted(?ApplicationAcceptedStateEnum $isApplicationAccepted): self
    {
        $this->isApplicationAccepted = $isApplicationAccepted;

        return $this;
    }
}