<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\FormFieldSortEnum;
use App\Model\Enum\Entity\FormFieldTypeEnum;

class FormFieldSearchData
{
    private string $phrase = '';

    private FormFieldSortEnum $sortBy = FormFieldSortEnum::CREATED_AT_DESC;

    private ?FormFieldTypeEnum $type = null;

    private ?bool $isRequired = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): FormFieldSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?FormFieldSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = FormFieldSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getType(): ?FormFieldTypeEnum
    {
        return $this->type;
    }

    public function setType(?FormFieldTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(?bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
    }
}