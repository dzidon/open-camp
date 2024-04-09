<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationAdminAttachmentSortEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationAdminAttachmentSearchData
{
    private array $validExtensions;

    private string $phrase = '';

    private ApplicationAdminAttachmentSortEnum $sortBy = ApplicationAdminAttachmentSortEnum::CREATED_AT_DESC;

    #[Assert\Choice(callback: 'getValidExtensions', multiple: true)]
    private array $extensions = [];

    public function __construct(array $validExtensions)
    {
        $this->validExtensions = $validExtensions;
    }

    public function getValidExtensions(): array
    {
        return $this->validExtensions;
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

    public function getSortBy(): ApplicationAdminAttachmentSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?ApplicationAdminAttachmentSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = ApplicationAdminAttachmentSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function setExtensions(?array $extensions): self
    {
        if ($extensions === null)
        {
            $extensions = [];
        }

        $this->extensions = $extensions;

        return $this;
    }
}