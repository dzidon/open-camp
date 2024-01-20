<?php

namespace App\Library\Data\User;

use App\Library\Constraint\DiscountSiblingIntervalInConfig;
use App\Model\Entity\PaymentMethod;
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

    /** @var ApplicationPurchasableItemData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemsData = [];

    #[Assert\Length(max: 1000)]
    private ?string $note;

    #[Assert\Length(max: 1000)]
    private ?string $customerChannel;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCustomerChannel(): ?string
    {
        return $this->customerChannel;
    }

    public function setCustomerChannel(?string $customerChannel): self
    {
        $this->customerChannel = $customerChannel;

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