<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\BlogPostSortEnum;
use App\Model\Entity\User;

class BlogPostSearchData
{
    private string $phrase = '';

    private BlogPostSortEnum $sortBy = BlogPostSortEnum::CREATED_AT_DESC;

    private ?User $author = null;

    private ?bool $isHidden = null;

    private ?bool $isPinned = null;

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function setPhrase(?string $phrase): self
    {
        $this->phrase = (string) $phrase;

        return $this;
    }

    public function getSortBy(): BlogPostSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?BlogPostSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = BlogPostSortEnum::CREATED_AT_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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

    public function isPinned(): ?bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(?bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }
}