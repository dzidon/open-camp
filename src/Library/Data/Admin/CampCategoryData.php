<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniqueCampCategory;
use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
#[UniqueCampCategory]
class CampCategoryData implements CampCategoryDataInterface
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    private ?CampCategory $parent = null;

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function setId(?UuidV4 $id): self
    {
        $this->id = $id;

        return $this;
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