<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueElementsInArray;
use App\Model\Entity\PurchasableItem;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class PurchasableItemVariantCreationData
{
    #[Assert\Valid]
    private PurchasableItemVariantData $purchasableItemVariantData;

    /** @var PurchasableItemVariantValueData[] */
    #[Assert\Valid]
    #[UniqueElementsInArray(fields: ['name'], message: 'unique_purchasable_item_variant_values')]
    #[Assert\NotBlank(message: 'purchasable_item_variant_values_mandatory')]
    private array $purchasableItemVariantValuesData = [];

    public function __construct(PurchasableItem $purchasableItem)
    {
        $this->purchasableItemVariantData = new PurchasableItemVariantData($purchasableItem);
    }

    public function getPurchasableItemVariantData(): PurchasableItemVariantData
    {
        return $this->purchasableItemVariantData;
    }

    public function getPurchasableItemVariantValuesData(): array
    {
        return $this->purchasableItemVariantValuesData;
    }

    public function setPurchasableItemVariantValuesData(array $purchasableItemVariantValuesData): self
    {
        foreach ($purchasableItemVariantValuesData as $purchasableItemVariantValueData)
        {
            if (!$purchasableItemVariantValueData instanceof PurchasableItemVariantValueData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, PurchasableItemVariantValueData::class)
                );
            }
        }

        $this->purchasableItemVariantValuesData = $purchasableItemVariantValuesData;

        return $this;
    }
    
    public function addPurchasableItemVariantValueData(PurchasableItemVariantValueData $purchasableItemVariantValueData): self
    {
        if (in_array($purchasableItemVariantValueData, $this->purchasableItemVariantValuesData, true))
        {
            return $this;
        }

        $this->purchasableItemVariantValuesData[] = $purchasableItemVariantValueData;

        return $this;
    }

    public function removePurchasableItemVariantValueData(PurchasableItemVariantValueData $purchasableItemVariantValueData): self
    {
        $key = array_search($purchasableItemVariantValueData, $this->purchasableItemVariantValuesData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->purchasableItemVariantValuesData[$key]);

        return $this;
    }
}