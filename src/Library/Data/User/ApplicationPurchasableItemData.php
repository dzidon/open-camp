<?php

namespace App\Library\Data\User;

use App\Library\Constraint\ApplicationPurchasableItemAmount;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationPurchasableItem;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

#[ApplicationPurchasableItemAmount]
class ApplicationPurchasableItemData
{
    private ApplicationPurchasableItem $applicationPurchasableItem;

    private ?ApplicationCamper $applicationCamper;

    /** @var ApplicationPurchasableItemInstanceData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemInstancesData = [];

    public function __construct(ApplicationPurchasableItem $applicationPurchasableItem, ?ApplicationCamper $applicationCamper = null)
    {
        $this->applicationPurchasableItem = $applicationPurchasableItem;
        $this->applicationCamper = $applicationCamper;
    }

    public function getApplicationPurchasableItem(): ApplicationPurchasableItem
    {
        return $this->applicationPurchasableItem;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
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