<?php

namespace App\Model\Event\User\ApplicationPurchasableItem;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item.create';

    private CampDatePurchasableItem $campDatePurchasableItem;

    private Application $application;

    private ?ApplicationPurchasableItem $applicationPurchasableItem = null;

    public function __construct(CampDatePurchasableItem $campDatePurchasableItem, Application $application)
    {
        $this->campDatePurchasableItem = $campDatePurchasableItem;
        $this->application = $application;
    }

    public function getCampDatePurchasableItem(): CampDatePurchasableItem
    {
        return $this->campDatePurchasableItem;
    }

    public function setCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem): self
    {
        $this->campDatePurchasableItem = $campDatePurchasableItem;

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

    public function getApplicationPurchasableItem(): ?ApplicationPurchasableItem
    {
        return $this->applicationPurchasableItem;
    }

    public function setApplicationPurchasableItem(?ApplicationPurchasableItem $applicationPurchasableItem): self
    {
        $this->applicationPurchasableItem = $applicationPurchasableItem;

        return $this;
    }
}