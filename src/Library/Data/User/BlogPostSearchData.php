<?php

namespace App\Library\Data\User;

use App\Library\Enum\Search\Data\User\BlogPostSortEnum;

class BlogPostSearchData
{
    private string $phrase = '';

    private BlogPostSortEnum $sortBy = BlogPostSortEnum::CREATED_AT_DESC;

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
}