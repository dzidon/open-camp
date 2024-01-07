<?php

namespace App\Library\Data\User;

use App\Model\Library\ApplicationTripLocation\ApplicationTripLocationArrayShape;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationCamperData
{
    private array $tripLocationsThere;

    private array $tripLocationsBack;

    private string $currency;

    #[Assert\Valid]
    private CamperData $camperData;

    #[Assert\When(
        expression: '!this.hasTripLocationsThere()',
        constraints: [
            new Assert\IsNull(),
        ],
    )]
    #[Assert\When(
        expression: 'this.hasTripLocationsThere()',
        constraints: [
            new Assert\Choice(callback: 'getTripLocationNamesThere'),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $tripLocationThere = null;

    #[Assert\When(
        expression: '!this.hasTripLocationsBack()',
        constraints: [
            new Assert\IsNull(),
        ],
    )]
    #[Assert\When(
        expression: 'this.hasTripLocationsBack()',
        constraints: [
            new Assert\Choice(callback: 'getTripLocationNamesBack'),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $tripLocationBack = null;

    /** @var ApplicationAttachmentData[] */
    #[Assert\Valid]
    private array $applicationAttachmentsData = [];

    /** @var ApplicationFormFieldValueData[] */
    #[Assert\Valid]
    private array $applicationFormFieldValuesData = [];

    public function __construct(bool $isNationalIdentifierEnabled, string $currency, array $tripLocationsThere, array $tripLocationsBack)
    {
        $tripLocationArrayShape = new ApplicationTripLocationArrayShape();

        foreach ($tripLocationsThere as $location)
        {
            $tripLocationArrayShape->assertLocationArrayShape($location);
        }

        foreach ($tripLocationsBack as $location)
        {
            $tripLocationArrayShape->assertLocationArrayShape($location);
        }

        $this->camperData = new CamperData($isNationalIdentifierEnabled);
        $this->currency = $currency;
        $this->tripLocationsThere = $tripLocationsThere;
        $this->tripLocationsBack = $tripLocationsBack;
    }

    public function getTripLocationsThere(): array
    {
        return $this->tripLocationsThere;
    }

    public function hasTripLocationsThere(): bool
    {
        return !empty($this->tripLocationsThere);
    }

    public function getTripLocationNamesThere(): array
    {
        $names = [];

        foreach ($this->tripLocationsThere as $tripLocationThere)
        {
            $names[] = $tripLocationThere['name'];
        }

        return $names;
    }

    public function getTripLocationsBack(): array
    {
        return $this->tripLocationsBack;
    }

    public function hasTripLocationsBack(): bool
    {
        return !empty($this->tripLocationsBack);
    }

    public function getTripLocationNamesBack(): array
    {
        $names = [];

        foreach ($this->tripLocationsBack as $tripLocationBack)
        {
            $names[] = $tripLocationBack['name'];
        }

        return $names;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCamperData(): CamperData
    {
        return $this->camperData;
    }

    public function getTripLocationThere(): ?string
    {
        return $this->tripLocationThere;
    }

    public function setTripLocationThere(?string $tripLocationThere): self
    {
        $this->tripLocationThere = $tripLocationThere;

        return $this;
    }

    public function getTripLocationBack(): ?string
    {
        return $this->tripLocationBack;
    }

    public function setTripLocationBack(?string $tripLocationBack): self
    {
        $this->tripLocationBack = $tripLocationBack;

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

    public function addApplicationFormFieldValuesDatum(ApplicationFormFieldValueData $applicationFormFieldValuesData): self
    {
        if (in_array($applicationFormFieldValuesData, $this->applicationFormFieldValuesData, true))
        {
            return $this;
        }

        $this->applicationFormFieldValuesData[] = $applicationFormFieldValuesData;

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