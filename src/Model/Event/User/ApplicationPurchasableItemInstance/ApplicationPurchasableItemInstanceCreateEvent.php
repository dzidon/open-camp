<?php

namespace App\Model\Event\User\ApplicationPurchasableItemInstance;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemInstanceCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item_instance.create';

    private ApplicationPurchasableItemInstanceData $data;

    private ApplicationPurchasableItem $applicationPurchasableItem;

    private ?ApplicationPurchasableItemInstance $applicationPurchasableItemInstance = null;

    private ?ApplicationCamper $applicationCamper = null;

    private int $priority;

    public function __construct(ApplicationPurchasableItemInstanceData $data,
                                ApplicationPurchasableItem             $applicationPurchasableItem,
                                int                                    $priority)
    {
        $this->data = $data;
        $this->applicationPurchasableItem = $applicationPurchasableItem;
        $this->priority = $priority;
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

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function setApplicationCamper(?ApplicationCamper $applicationCamper): self
    {
        $this->applicationCamper = $applicationCamper;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}