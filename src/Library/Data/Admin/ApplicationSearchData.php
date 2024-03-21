<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use App\Library\Enum\Search\Data\Admin\ApplicationSortEnum;

class ApplicationSearchData
{
    private string $phrase = '';

    private ApplicationSortEnum $sortBy = ApplicationSortEnum::COMPLETED_AT_DESC;

    private ?bool $isOnlinePaymentMethod = null;
    
    private ?ApplicationAcceptedStateEnum $isAccepted = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): ApplicationSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationSortEnum::COMPLETED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function isOnlinePaymentMethod(): ?bool
    {
        return $this->isOnlinePaymentMethod;
    }

    public function setIsOnlinePaymentMethod(?bool $isOnlinePaymentMethod): self
    {
        $this->isOnlinePaymentMethod = $isOnlinePaymentMethod;

        return $this;
    }
    
    public function getIsAccepted(): ?ApplicationAcceptedStateEnum
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?ApplicationAcceptedStateEnum $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }
}