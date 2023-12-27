<?php

namespace App\Library\Data\User;

use App\Library\Constraint\Compound\StreetRequirements;
use App\Library\Constraint\Compound\ZipCodeRequirements;
use App\Library\Constraint\EuCin;
use App\Library\Constraint\EuVatId;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationStepOneData
{
    private bool $isEuBusinessDataEnabled;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

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

    /** @var ContactData[] */
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'application_contacts_mandatory')]
    private array $contactsData = [];

    /** @var ApplicationCamperData[] */
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'application_campers_mandatory')]
    private array $applicationCampersData = [];

    /** @var ApplicationAttachmentData[] */
    #[Assert\Valid]
    private array $applicationAttachmentsData = [];

    /** @var ApplicationFormFieldValueData[] */
    #[Assert\Valid]
    private array $applicationFormFieldValuesData = [];

    public function __construct(bool   $isEuBusinessDataEnabled,
                                bool   $isNationalIdentifierEnabled,
                                string $currency)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
    }

    public function isEuBusinessDataEnabled(): bool
    {
        return $this->isEuBusinessDataEnabled;
    }

    public function isNationalIdentifierEnabled(): bool
    {
        return $this->isNationalIdentifierEnabled;
    }

    public function getCurrency(): string
    {
        return $this->currency;
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

    public function getContactsData(): array
    {
        return $this->contactsData;
    }

    public function setContactsData(array $contactsData): self
    {
        foreach ($contactsData as $contactData)
        {
            if (!$contactData instanceof ContactData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, ContactData::class)
                );
            }
        }

        $this->contactsData = $contactsData;

        return $this;
    }

    public function addContactData(ContactData $contactData): self
    {
        if (in_array($contactData, $this->contactsData, true))
        {
            return $this;
        }

        $this->contactsData[] = $contactData;

        return $this;
    }

    public function removeContactData(ContactData $contactData): self
    {
        $key = array_search($contactData, $this->contactsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->contactsData[$key]);

        return $this;
    }

    public function getApplicationCampersData(): array
    {
        return $this->applicationCampersData;
    }

    public function setApplicationCampersData(array $applicationCampersData): self
    {
        foreach ($applicationCampersData as $applicationCamperData)
        {
            if (!$applicationCamperData instanceof ApplicationCamperData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, ApplicationCamperData::class)
                );
            }
        }

        $this->applicationCampersData = $applicationCampersData;

        return $this;
    }

    public function addApplicationCamperData(ApplicationCamperData $applicationCamperData): self
    {
        if (in_array($applicationCamperData, $this->applicationCampersData, true))
        {
            return $this;
        }

        $this->applicationCampersData[] = $applicationCamperData;

        return $this;
    }

    public function removeApplicationCamperData(ApplicationCamperData $applicationCamperData): self
    {
        $key = array_search($applicationCamperData, $this->applicationCampersData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationCampersData[$key]);

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
}