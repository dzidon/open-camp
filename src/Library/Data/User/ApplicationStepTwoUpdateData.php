<?php

namespace App\Library\Data\User;

use App\Model\Entity\PaymentMethod;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationStepTwoUpdateData
{
    #[Assert\NotBlank]
    private ?PaymentMethod $paymentMethod = null;

    /** @var ApplicationPurchasableItemData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemsData = [];

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
}