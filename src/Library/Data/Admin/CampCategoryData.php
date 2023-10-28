<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniqueCampCategory;
use App\Model\Entity\CampCategory;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueCampCategory]
class CampCategoryData
{
    private ?CampCategory $campCategory;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    private ?CampCategory $parent = null;

    public function __construct(?CampCategory $campCategory = null)
    {
        $this->campCategory = $campCategory;
    }

    public function getCampCategory(): ?CampCategory
    {
        return $this->campCategory;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getParent(): ?CampCategory
    {
        return $this->parent;
    }

    public function setParent(?CampCategory $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}