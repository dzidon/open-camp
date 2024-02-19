<?php

namespace App\Library\Data\User;

use App\Library\Constraint\ApplicationPurchasableItemVariantsNotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationPurchasableItemVariantsNotBlank]
class ApplicationPurchasableItemInstanceData
{
    private int $maxAmount;

    private int $amount = 1;

    /** @var ApplicationPurchasableItemVariantData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemVariantsData = [];

    public function __construct(int $maxAmount)
    {
        $this->maxAmount = $maxAmount;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }
    
    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = (int) $amount;

        return $this;
    }

    public function getChosenApplicationPurchasableItemVariants(): array
    {
        $variants = [];

        foreach ($this->applicationPurchasableItemVariantsData as $applicationPurchasableItemVariantData)
        {
            $label = $applicationPurchasableItemVariantData->getLabel();
            $value = $applicationPurchasableItemVariantData->getValue();
            $variants[$label] = $value;
        }

        return $variants;
    }

    public function getApplicationPurchasableItemVariantsData(): array
    {
        return $this->applicationPurchasableItemVariantsData;
    }

    public function addApplicationPurchasableItemVariantsDatum(ApplicationPurchasableItemVariantData $applicationPurchasableItemVariantData): self
    {
        if (in_array($applicationPurchasableItemVariantData, $this->applicationPurchasableItemVariantsData, true))
        {
            return $this;
        }

        $this->applicationPurchasableItemVariantsData[] = $applicationPurchasableItemVariantData;

        return $this;
    }

    public function removeApplicationPurchasableItemVariantsDatum(ApplicationPurchasableItemVariantData $applicationPurchasableItemVariantData): self
    {
        $key = array_search($applicationPurchasableItemVariantData, $this->applicationPurchasableItemVariantsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationPurchasableItemVariantsData[$key]);

        return $this;
    }
}