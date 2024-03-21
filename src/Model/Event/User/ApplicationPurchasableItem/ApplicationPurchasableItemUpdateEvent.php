<?php

namespace App\Model\Event\User\ApplicationPurchasableItem;

use App\Library\Data\Common\ApplicationPurchasableItemData;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item.update';

    private ApplicationPurchasableItemData $data;

    private ApplicationPurchasableItem $applicationPurchasableItem;

    public function __construct(ApplicationPurchasableItemData $data, ApplicationPurchasableItem $applicationPurchasableItem)
    {
        $this->data = $data;
        $this->applicationPurchasableItem = $applicationPurchasableItem;
    }

    public function getApplicationPurchasableItemData(): ApplicationPurchasableItemData
    {
        return $this->data;
    }

    public function setApplicationPurchasableItemData(ApplicationPurchasableItemData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplicationPurchasableItem(): ApplicationPurchasableItem
    {
        return $this->applicationPurchasableItem;
    }

    public function setApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): self
    {
        $this->applicationPurchasableItem = $applicationPurchasableItem;

        return $this;
    }
}