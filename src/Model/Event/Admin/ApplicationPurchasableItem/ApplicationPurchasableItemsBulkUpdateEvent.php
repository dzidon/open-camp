<?php

namespace App\Model\Event\Admin\ApplicationPurchasableItem;

use App\Library\Data\Admin\ApplicationPurchasableItemsData;
use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemsBulkUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_purchasable_item.bulk_update';

    private ApplicationPurchasableItemsData $data;

    private Application $application;

    public function __construct(ApplicationPurchasableItemsData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationPurchasableItemsData(): ApplicationPurchasableItemsData
    {
        return $this->data;
    }

    public function setApplicationPurchasableItemsData(ApplicationPurchasableItemsData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}