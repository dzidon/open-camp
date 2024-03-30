<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\StreetRequirements;
use App\Library\Constraint\Compound\ZipCodeRequirements;
use App\Library\Constraint\EuCin;
use App\Library\Constraint\EuVatId;
use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationDiscountsData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Application;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationData
{
    private Application $application;

    private ?bool $isAccepted = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameFirst = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $nameLast = null;

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

    private bool $isCompany = false;

    #[Assert\When(
        expression: 'this.getApplication().isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 255),
        ],
    )]
    private ?string $businessName = null;

    #[Assert\When(
        expression: 'this.getApplication().isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 32),
            new EuCin(),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $businessCin = null;

    #[Assert\When(
        expression: 'this.getApplication().isEuBusinessDataEnabled() and this.isCompany()',
        constraints: [
            new Assert\Length(max: 32),
            new EuVatId(),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $businessVatId = null;

    /** @var ApplicationAttachmentData[] */
    #[Assert\Valid]
    private array $applicationAttachmentsData = [];

    /** @var ApplicationFormFieldValueData[] */
    #[Assert\Valid]
    private array $applicationFormFieldValuesData = [];

    #[Assert\Valid]
    private ApplicationDiscountsData $applicationDiscountsData;

    public function __construct(Application $application)
    {
        $this->application = $application;

        $this->applicationDiscountsData = new ApplicationDiscountsData(
            $this->application->getCurrency(),
            $this->application->getDiscountSiblingsConfig(),
            count($this->application->getApplicationCampers()),
        );
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function isAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getApplicationAttachmentsData(): array
    {
        return $this->applicationAttachmentsData;
    }

    public function addApplicationAttachmentsDatum(ApplicationAttachmentData $applicationAttachmentData): self
    {
        if (in_array($applicationAttachmentData, $this->applicationAttachmentsData, true))
        {
            return $this;
        }

        $this->applicationAttachmentsData[] = $applicationAttachmentData;

        return $this;
    }

    public function removeApplicationAttachmentsDatum(ApplicationAttachmentData $applicationAttachmentData): self
    {
        $key = array_search($applicationAttachmentData, $this->applicationAttachmentsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationAttachmentsData[$key]);

        return $this;
    }

    public function getApplicationFormFieldValuesData(): array
    {
        return $this->applicationFormFieldValuesData;
    }

    public function addApplicationFormFieldValuesDatum(ApplicationFormFieldValueData $applicationFormFieldValueData): self
    {
        if (in_array($applicationFormFieldValueData, $this->applicationFormFieldValuesData, true))
        {
            return $this;
        }

        $this->applicationFormFieldValuesData[] = $applicationFormFieldValueData;

        return $this;
    }

    public function removeApplicationFormFieldValuesDatum(ApplicationFormFieldValueData $applicationFormFieldValueData): self
    {
        $key = array_search($applicationFormFieldValueData, $this->applicationFormFieldValuesData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationFormFieldValuesData[$key]);

        return $this;
    }

    public function getApplicationDiscountsData(): ApplicationDiscountsData
    {
        return $this->applicationDiscountsData;
    }
}