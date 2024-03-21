<?php

namespace App\Model\Event\User\ApplicationPurchasableItemInstance;

use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemInstanceUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item_instance.update';

    private ApplicationPurchasableItemInstanceData $data;

    private ApplicationPurchasableItemInstance $applicationPurchasableItemInstance;

    public function __construct(ApplicationPurchasableItemInstanceData $data, ApplicationPurchasableItemInstance $applicationPurchasableItemInstance)
    {
        $this->data = $data;
        $this->applicationPurchasableItemInstance = $applicationPurchasableItemInstance;
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

    public function getApplicationPurchasableItemInstance(): ApplicationPurchasableItemInstance
    {
        return $this->applicationPurchasableItemInstance;
    }

    public function setApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance): self
    {
        $this->applicationPurchasableItemInstance = $applicationPurchasableItemInstance;

        return $this;
    }
}