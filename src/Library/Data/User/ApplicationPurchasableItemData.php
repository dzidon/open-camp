<?php

namespace App\Library\Data\User;

use App\Library\Constraint\ApplicationPurchasableItemAmount;
use App\Model\Entity\ApplicationPurchasableItem;
use Symfony\Component\Validator\Constraints as Assert;
use LogicException;

#[ApplicationPurchasableItemAmount]
class ApplicationPurchasableItemData
{
    private ApplicationPurchasableItem $applicationPurchasableItem;

    /** @var ApplicationPurchasableItemInstanceData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemInstancesData = [];

    public function __construct(ApplicationPurchasableItem $applicationPurchasableItem)
    {
        $this->applicationPurchasableItem = $applicationPurchasableItem;
    }

    public function getApplicationPurchasableItem(): ApplicationPurchasableItem
    {
        return $this->applicationPurchasableItem;
    }

    public function getApplicationPurchasableItemInstancesData(): array
    {
        return $this->applicationPurchasableItemInstancesData;
    }

    public function setApplicationPurchasableItemInstancesData(array $applicationPurchasableItemInstancesData): self
    {
        foreach ($applicationPurchasableItemInstancesData as $applicationPurchasableItemInstanceData)
        {
            if (!$applicationPurchasableItemInstanceData instanceof ApplicationPurchasableItemInstanceData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, ApplicationPurchasableItemInstanceData::class)
                );
            }
        }

        $this->applicationPurchasableItemInstancesData = $applicationPurchasableItemInstancesData;

        return $this;
    }

    public function addApplicationPurchasableItemInstanceData(ApplicationPurchasableItemInstanceData $applicationPurchasableItemInstanceData): self
    {
        if (in_array($applicationPurchasableItemInstanceData, $this->applicationPurchasableItemInstancesData, true))
        {
            return $this;
        }

        $this->applicationPurchasableItemInstancesData[] = $applicationPurchasableItemInstanceData;

        return $this;
    }

    public function removeApplicationPurchasableItemInstanceData(ApplicationPurchasableItemInstanceData $applicationPurchasableItemInstanceData): self
    {
        $key = array_search($applicationPurchasableItemInstanceData, $this->applicationPurchasableItemInstancesData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->applicationPurchasableItemInstancesData[$key]);

        return $this;
    }
}