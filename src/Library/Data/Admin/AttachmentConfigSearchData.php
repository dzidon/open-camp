<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\AttachmentConfigSortEnum;
use App\Model\Entity\FileExtension;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;

class AttachmentConfigSearchData
{
    private string $phrase = '';

    private AttachmentConfigSortEnum $sortBy = AttachmentConfigSortEnum::CREATED_AT_DESC;

    private ?AttachmentConfigRequiredTypeEnum $requiredType = null;

    /** @var FileExtension[] */
    private array $fileExtensions = [];

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): AttachmentConfigSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?AttachmentConfigSortEnum $sortBy): void
    {
        if ($sortBy === null)
        {
            $sortBy = AttachmentConfigSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;
    }

    public function getRequiredType(): ?AttachmentConfigRequiredTypeEnum
    {
        return $this->requiredType;
    }

    public function setRequiredType(?AttachmentConfigRequiredTypeEnum $requiredType): self
    {
        $this->requiredType = $requiredType;

        return $this;
    }

    public function getFileExtensions(): array
    {
        return $this->fileExtensions;
    }

    public function addFileExtension(FileExtension $fileExtension): self
    {
        if (in_array($fileExtension, $this->fileExtensions, true))
        {
            return $this;
        }

        $this->fileExtensions[] = $fileExtension;

        return $this;
    }

    public function removeFileExtension(FileExtension $fileExtension): self
    {
        $key = array_search($fileExtension, $this->fileExtensions, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->fileExtensions[$key]);

        return $this;
    }
}