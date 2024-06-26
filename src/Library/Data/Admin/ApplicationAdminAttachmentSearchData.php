<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\ApplicationAdminAttachmentSortEnum;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationAdminAttachmentSearchData
{
    /** @var string[] */
    private array $validExtensions;

    private string $phrase = '';

    private ApplicationAdminAttachmentSortEnum $sortBy = ApplicationAdminAttachmentSortEnum::CREATED_AT_DESC;

    /** @var string[] */
    #[Assert\Choice(callback: 'getValidExtensions', multiple: true)]
    private array $extensions = [];

    public function __construct(array $validExtensions)
    {
        foreach ($validExtensions as $validExtension)
        {
            if (!is_string($validExtension))
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain strings.', __METHOD__)
                );
            }
        }

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

        foreach ($extensions as $extension)
        {
            if (!is_string($extension))
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain strings.', __METHOD__)
                );
            }
        }

        $this->extensions = $extensions;

        return $this;
    }
}