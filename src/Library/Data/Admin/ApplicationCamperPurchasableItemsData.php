<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\ApplicationCamper;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationCamperPurchasableItemsData
{
    /** @var ApplicationPurchasableItemData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemsData = [];

    public function getApplicationCamper(): ?ApplicationCamper
    {
        foreach ($this->applicationPurchasableItemsData as $applicationPurchasableItemData)
        {
            return $applicationPurchasableItemData->getApplicationCamper();
        }

        return null;
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

        $applicationCamper = $applicationPurchasableItemData->getApplicationCamper();

        if ($applicationCamper === null)
        {
            throw new LogicException(
                sprintf('All instances passed to %s must reference %s. You passed an instance that references null.', __METHOD__, ApplicationCamper::class)
            );
        }

        foreach ($this->applicationPurchasableItemsData as $existingApplicationPurchasableItemData)
        {
            $existingApplicationCamper = $existingApplicationPurchasableItemData->getApplicationCamper();

            if ($applicationCamper !== $existingApplicationCamper)
            {
                throw new LogicException(
                    sprintf('All instances passed to %s must reference the same %s instance.', __METHOD__, ApplicationCamper::class)
                );
            }
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