<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationPaymentSortEnum;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;

class ApplicationPaymentSearchData
{
    private ?ApplicationPaymentTypeEnum $type = null;

    private ?bool $isOnline = null;

    private ApplicationPaymentSortEnum $sortBy = ApplicationPaymentSortEnum::CREATED_AT_DESC;

    public function getType(): ?ApplicationPaymentTypeEnum
    {
        return $this->type;
    }

    public function setType(?ApplicationPaymentTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(?bool $isOnline): self
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function getSortBy(): ApplicationPaymentSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationPaymentSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationPaymentSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }
}