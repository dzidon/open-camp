<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\Compound\StreetRequirements;
use App\Library\Constraint\Compound\ZipCodeRequirements;
use App\Library\Constraint\UniqueCamp;
use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueCamp]
class CampData
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\NotBlank]
    private ?int $ageMin = null;

    #[Assert\GreaterThanOrEqual(propertyPath: 'ageMin')]
    #[Assert\NotBlank]
    private ?int $ageMax = null;

    #[StreetRequirements]
    #[Assert\NotBlank]
    private ?string $street = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $town = null;

    #[ZipCodeRequirements]
    #[Assert\NotBlank]
    private ?string $zip = null;

    #[Assert\Country]
    #[Assert\NotBlank]
    private ?string $country = null;

    #[Assert\Length(max: 160)]
    private ?string $descriptionShort = null;

    #[Assert\Length(max: 5000)]
    private ?string $descriptionLong = null;

    private ?int $featuredPriority = null;

    private ?CampCategory $campCategory = null;

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

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(?int $ageMin): self
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->ageMax;
    }

    public function setAgeMax(?int $ageMax): self
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->descriptionShort;
    }

    public function setDescriptionShort(?string $descriptionShort): self
    {
        $this->descriptionShort = $descriptionShort;

        return $this;
    }

    public function getDescriptionLong(): ?string
    {
        return $this->descriptionLong;
    }

    public function setDescriptionLong(?string $descriptionLong): self
    {
        $this->descriptionLong = $descriptionLong;

        return $this;
    }

    public function getFeaturedPriority(): ?int
    {
        return $this->featuredPriority;
    }

    public function setFeaturedPriority(?int $featuredPriority): self
    {
        $this->featuredPriority = $featuredPriority;

        return $this;
    }

    public function getCampCategory(): ?CampCategory
    {
        return $this->campCategory;
    }

    public function setCampCategory(?CampCategory $campCategory): self
    {
        $this->campCategory = $campCategory;

        return $this;
    }
}