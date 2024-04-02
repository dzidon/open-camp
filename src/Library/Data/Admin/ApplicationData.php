<?php

namespace App\Library\Data\Admin;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationDiscountsData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Library\Data\Common\BillingData;
use App\Model\Entity\Application;
use App\Model\Enum\Entity\ApplicationCustomerChannelEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationData
{
    private Application $application;

    private ?bool $isAccepted = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[Assert\Valid]
    private BillingData $billingData;

    #[Assert\Length(max: 1000)]
    private ?string $note;

    private ?ApplicationCustomerChannelEnum $customerChannel = null;

    #[Assert\When(
        expression: 'this.getCustomerChannelOther() === enum("App\\\Model\\\Enum\\\Entity\\\ApplicationCustomerChannelEnum::OTHER")',
        constraints: [
            new Assert\Length(max: 255),
            new Assert\NotBlank(),
        ],
    )]
    private ?string $customerChannelOther = null;

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
        $this->billingData = new BillingData(true, $this->application->isEuBusinessDataEnabled());
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

    public function getBillingData(): BillingData
    {
        return $this->billingData;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCustomerChannel(): ?ApplicationCustomerChannelEnum
    {
        return $this->customerChannel;
    }

    public function setCustomerChannel(?ApplicationCustomerChannelEnum $customerChannel): self
    {
        $this->customerChannel = $customerChannel;

        return $this;
    }

    public function getCustomerChannelOther(): ?string
    {
        return $this->customerChannelOther;
    }

    public function setCustomerChannelOther(?string $customerChannelOther): self
    {
        $this->customerChannelOther = $customerChannelOther;

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