<?php

namespace App\Model\Event\User\ApplicationPurchasableItemInstance;

use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemInstanceVariantUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item_instance.variant_update';

    private ApplicationPurchasableItemVariantData $data;

    private ApplicationPurchasableItemInstance $applicationPurchasableItemInstance;

    public function __construct(ApplicationPurchasableItemVariantData $data, ApplicationPurchasableItemInstance $applicationPurchasableItemInstance)
    {
        $this->data = $data;
        $this->applicationPurchasableItemInstance = $applicationPurchasableItemInstance;
    }

    public function getApplicationPurchasableItemVariantData(): ApplicationPurchasableItemVariantData
    {
        return $this->data;
    }

    public function setApplicationPurchasableItemVariantData(ApplicationPurchasableItemVariantData $data): self
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