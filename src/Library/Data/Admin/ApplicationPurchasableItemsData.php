<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class ApplicationPurchasableItemsData
{
    /** @var ApplicationCamperPurchasableItemsData[] */
    #[Assert\Valid]
    private array $applicationCamperPurchasableItemsData = [];

    /** @var ApplicationPurchasableItemData[] */
    #[Assert\Valid]
    private array $applicationPurchasableItemsData = [];

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