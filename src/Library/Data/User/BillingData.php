<?php

namespace App\Library\Data\User;

use App\Library\Constraint\Compound\StreetRequirements;
use App\Library\Constraint\Compound\ZipCodeRequirements;
use App\Library\Constraint\EuCin;
use App\Library\Constraint\EuVatId;
use Symfony\Component\Validator\Constraints as Assert;

class BillingData
{
    #[Assert\Length(max: 255)]
    #[Assert\When(
        expression: 'this.getNameLast() !== null',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\When(
        expression: 'this.getNameFirst() !== null',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?string $nameLast = null;

    #[StreetRequirements]
    private ?string $street = null;

    #[Assert\Length(max: 255)]
    private ?string $town = null;

    #[ZipCodeRequirements]
    private ?string $zip = null;

    #[Assert\Country]
    private ?string $country = null;

    private bool $isCompany = false;

    #[Assert\When(
        expression: 'this.isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 255),
        ],
    )]
    private ?string $businessName = null;

    #[Assert\When(
        expression: 'this.isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 32),
            new EuCin(),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $businessCin = null;

    #[Assert\When(
        expression: 'this.isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 32),
            new EuVatId(),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $businessVatId = null;

    private bool $isEuBusinessDataEnabled;

    public function __construct(bool $isEuBusinessDataEnabled)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
    }

    public function isEuBusinessDataEnabled(): bool
    {
        return $this->isEuBusinessDataEnabled;
    }

    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(?string $nameFirst): self
    {
        $this->nameFirst = $nameFirst;

        return $this;
    }

    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    public function setNameLast(?string $nameLast): self
    {
        $this->nameLast = $nameLast;

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

    public function isCompany(): bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): self
    {
        $this->isCompany = $isCompany;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getBusinessCin(): ?string
    {
        return $this->businessCin;
    }

    public function setBusinessCin(?string $businessCin): self
    {
        $this->businessCin = $businessCin;

        return $this;
    }

    public function getBusinessVatId(): ?string
    {
        return $this->businessVatId;
    }

    public function setBusinessVatId(?string $businessVatId): self
    {
        $this->businessVatId = $businessVatId;

        return $this;
    }
}