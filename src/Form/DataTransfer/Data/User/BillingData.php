<?php

namespace App\Form\DataTransfer\Data\User;

use App\Validator\Constraint\Compound\StreetRequirements;
use App\Validator\Constraint\Compound\ZipCodeRequirements;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class BillingData implements BillingDataInterface
{
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[StreetRequirements]
    private ?string $street = null;

    #[Assert\Length(max: 255)]
    private ?string $town = null;

    #[ZipCodeRequirements]
    private ?string $zip = null;

    #[Assert\Country]
    private ?string $country = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
}