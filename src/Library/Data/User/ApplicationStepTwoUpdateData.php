<?php

namespace App\Library\Data\User;

use App\Library\Constraint\DiscountSiblingIntervalInConfig;
use App\Model\Entity\PaymentMethod;
use App\Model\Enum\Entity\ApplicationCustomerChannelEnum;
use App\Model\Library\DiscountConfig\DiscountConfigArrayShape;
use App\Model\Library\DiscountConfig\DiscountSiblingsIntervalShape;
use Symfony\Component\Validator\Constraints as Assert;

#[DiscountSiblingIntervalInConfig]
class ApplicationStepTwoUpdateData
{
    private array $discountSiblingsConfig;

    private string $currency;

    private int $numberOfApplicationCampers;

    #[Assert\NotBlank]
    private ?PaymentMethod $paymentMethod = null;

    /** @var ApplicationCamperPurchasableItemsData[] */
    #[Assert\Valid]
    private array $applicationCamperPurchasableItemsData = [];

    /** @var ApplicationPurchasableItemData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemsData = [];

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

    private false|array $discountSiblingsInterval = false;

    public function __construct(array $discountSiblingsConfig, string $currency, int $numberOfApplicationCampers)
    {
        $this->discountSiblingsConfig = $discountSiblingsConfig;
        $this->currency = $currency;
        $this->numberOfApplicationCampers = $numberOfApplicationCampers;

        $discountConfigArrayShape = new DiscountConfigArrayShape();
        $discountConfigArrayShape->assertSiblingsConfig($this->discountSiblingsConfig);
    }

    public function getDiscountSiblingsConfig(): array
    {
        return $this->discountSiblingsConfig;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getNumberOfApplicationCampers(): string
    {
        return $this->numberOfApplicationCampers;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getApplicationPurchasableItemsData(): array
    {
        return $this->applicationPurchasableItemsData;
    }

    public function addApplicationPurchasableItemsDatum(ApplicationPurchasableItemData $applicationPurchasableItemData): self
    {
        if (in_array($applicationPurchasableItemData, $this->applicationPurchasableItemsData, true))
        {
            return $this;
        }

        $this->applicationPurchasableItemsData[] = $applicationPurchasableItemData;

        return $this;
    }

    public function removeApplicationPurchasableItemsDatum(ApplicationPurchasableItemData $applicationPurchasableItemData): self
    {
        $key = array_search($applicationPurchasableItemData, $this->applicationPurchasableItemsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationPurchasableItemsData[$key]);

        return $this;
    }

    public function getApplicationCamperPurchasableItemsData(): array
    {
        return $this->applicationCamperPurchasableItemsData;
    }

    public function addApplicationCamperPurchasableItemsDatum(ApplicationCamperPurchasableItemsData $applicationCamperPurchasableItemsData): self
    {
        if (in_array($applicationCamperPurchasableItemsData, $this->applicationCamperPurchasableItemsData, true))
        {
            return $this;
        }

        $this->applicationCamperPurchasableItemsData[] = $applicationCamperPurchasableItemsData;

        return $this;
    }

    public function removeApplicationCamperPurchasableItemsDatum(ApplicationCamperPurchasableItemsData $applicationCamperPurchasableItemsData): self
    {
        $key = array_search($applicationCamperPurchasableItemsData, $this->applicationCamperPurchasableItemsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationCamperPurchasableItemsData[$key]);

        return $this;
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

    public function getDiscountSiblingsInterval(): false|array
    {
        return $this->discountSiblingsInterval;
    }

    public function setDiscountSiblingsInterval(false|array $discountSiblingsInterval): self
    {
        $this->discountSiblingsInterval = $discountSiblingsInterval;

        if ($this->discountSiblingsInterval !== false)
        {
            $discountSiblingsIntervalShape = new DiscountSiblingsIntervalShape();
            $discountSiblingsIntervalShape->assertDiscountSiblingsInterval($this->discountSiblingsInterval);
        }

        return $this;
    }
}