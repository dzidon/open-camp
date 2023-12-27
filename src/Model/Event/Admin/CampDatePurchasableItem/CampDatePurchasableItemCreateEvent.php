<?php

namespace App\Model\Event\Admin\CampDatePurchasableItem;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Event\AbstractModelEvent;

class CampDatePurchasableItemCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_purchasable_item.create';

    private CampDatePurchasableItemData $data;

    private CampDate $campDate;

    private ?CampDatePurchasableItem $entity = null;

    public function __construct(CampDatePurchasableItemData $data, CampDate $campDate)
    {
        $this->data = $data;
        $this->campDate = $campDate;
    }

    public function getCampDatePurchasableItemData(): CampDatePurchasableItemData
    {
        return $this->data;
    }

    public function setCampDatePurchasableItemData(CampDatePurchasableItemData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampDate(): CampDate
    {
        return $this->campDate;
    }

    public function setCampDate(CampDate $campDate): self
    {
        $this->campDate = $campDate;

        return $this;
    }

    public function getCampDatePurchasableItem(): ?CampDatePurchasableItem
    {
        return $this->entity;
    }

    public function setCampDatePurchasableItem(?CampDatePurchasableItem $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}