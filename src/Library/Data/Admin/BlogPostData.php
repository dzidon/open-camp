<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniqueBlogPost;
use App\Model\Entity\BlogPost;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueBlogPost]
class BlogPostData
{
    private ?BlogPost $blogPost;

    #[Assert\Length(max: 32)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    #[Assert\Length(max: 160)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[Assert\Length(max: 5000)]
    #[Assert\NotBlank]
    private ?string $content = null;

    private bool $isHidden = false;

    private bool $isPinned = false;

    public function __construct(?BlogPost $blogPost = null)
    {
        $this->blogPost = $blogPost;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrlName(): ?string
    {
        return $this->urlName;
    }

    public function setUrlName(?string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }
}