<?php

namespace App\Library\Data\User;

use App\Library\Constraint\ApplicationCampersCount;
use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Library\Data\Common\BillingData;
use App\Library\Data\Common\ContactData;
use App\Model\Entity\CampDate;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationCampersCount]
class ApplicationStepOneData
{
    private CampDate $campDate;

    private bool $isEuBusinessDataEnabled;

    private bool $isNationalIdentifierEnabled;

    private string $currency;

    private float $tax;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Assert\Valid]
    private BillingData $billingData;

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

    public function __construct(bool     $isEuBusinessDataEnabled,
                                bool     $isNationalIdentifierEnabled,
                                string   $currency,
                                float    $tax,
                                CampDate $campDate)
    {
        $this->billingData = new BillingData(true, $isEuBusinessDataEnabled);
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
        $this->currency = $currency;
        $this->tax = $tax;
        $this->campDate = $campDate;
    }

    public function getCampDate(): CampDate
    {
        return $this->campDate;
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

    public function getTax(): string
    {
        return $this->tax;
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

    public function getBillingData(): BillingData
    {
        return $this->billingData;
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