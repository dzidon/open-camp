<?php

namespace App\Model\Event\Admin\CampDatePurchasableItem;

use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Event\AbstractModelEvent;

class CampDatePurchasableItemDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_purchasable_item.delete';

    private CampDatePurchasableItem $entity;

    public function __construct(CampDatePurchasableItem $entity)
    {
        $this->entity = $entity;
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