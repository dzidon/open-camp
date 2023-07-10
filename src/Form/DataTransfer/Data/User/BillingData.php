<?php

namespace App\Form\DataTransfer\Data\User;

use App\Form\Type\User\BillingType;
use App\Validator\Compound as CompoundAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link BillingType}
 */
class BillingData implements BillingDataInterface
{
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[CompoundAssert\StreetRequirements]
    private ?string $street = null;

    #[Assert\Length(max: 255)]
    private ?string $town = null;

    #[CompoundAssert\ZipCodeRequirements]
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