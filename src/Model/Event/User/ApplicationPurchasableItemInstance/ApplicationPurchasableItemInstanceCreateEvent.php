<?php

namespace App\Model\Event\User\ApplicationPurchasableItemInstance;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemInstanceCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item_instance.create';

    private ApplicationPurchasableItemInstanceData $data;

    private ApplicationPurchasableItem $applicationPurchasableItem;

    private ?ApplicationPurchasableItemInstance $applicationPurchasableItemInstance = null;

    public function __construct(ApplicationPurchasableItemInstanceData $data, ApplicationPurchasableItem $applicationPurchasableItem)
    {
        $this->data = $data;
        $this->applicationPurchasableItem = $applicationPurchasableItem;
    }

    public function getApplicationPurchasableItemInstanceData(): ApplicationPurchasableItemInstanceData
    {
        return $this->data;
    }

    public function setApplicationPurchasableItemInstanceData(ApplicationPurchasableItemInstanceData $data): self
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

    public function getApplicationPurchasableItemInstance(): ?ApplicationPurchasableItemInstance
    {
        return $this->applicationPurchasableItemInstance;
    }

    public function setApplicationPurchasableItemInstance(?ApplicationPurchasableItemInstance $applicationPurchasableItemInstance): self
    {
        $this->applicationPurchasableItemInstance = $applicationPurchasableItemInstance;

        return $this;
    }
}