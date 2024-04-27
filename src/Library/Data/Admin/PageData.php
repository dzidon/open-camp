<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniquePage;
use App\Model\Entity\Page;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePage]
class PageData
{
    private ?Page $page;

    #[Assert\Length(max: 32)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    #[Assert\Length(max: 5000)]
    #[Assert\NotBlank]
    private ?string $content = null;

    private bool $isHidden = false;

    private bool $isInMenu = false;

    #[Assert\When(
        expression: 'this.isInMenu()',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?int $menuPriority = null;

    public function __construct(?Page $page = null)
    {
        $this->page = $page;
    }

    public function getPage(): ?Page
    {
        return $this->page;
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

    public function isInMenu(): bool
    {
        return $this->isInMenu;
    }

    public function setIsInMenu(bool $isInMenu): self
    {
        $this->isInMenu = $isInMenu;

        return $this;
    }

    public function getMenuPriority(): ?int
    {
        return $this->menuPriority;
    }

    public function setMenuPriority(?int $menuPriority): self
    {
        $this->menuPriority = $menuPriority;

        return $this;
    }
}