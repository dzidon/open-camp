<?php

namespace App\Model\Event\Admin\CampDatePurchasableItem;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Event\AbstractModelEvent;

class CampDatePurchasableItemUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_purchasable_item.update';

    private CampDatePurchasableItemData $data;

    private CampDatePurchasableItem $entity;

    public function __construct(CampDatePurchasableItemData $data, CampDatePurchasableItem $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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

    public function getCampDatePurchasableItem(): CampDatePurchasableItem
    {
        return $this->entity;
    }

    public function setCampDatePurchasableItem(CampDatePurchasableItem $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}