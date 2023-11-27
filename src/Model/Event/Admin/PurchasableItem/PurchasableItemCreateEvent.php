<?php

namespace App\Model\Event\Admin\PurchasableItem;

use App\Library\Data\Admin\PurchasableItemData;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasableItemCreateEvent extends Event
{
    public const NAME = 'model.admin.purchasable_item.create';

    private PurchasableItemData $data;

    public function __construct(PurchasableItemData $data)
    {
        $this->data = $data;
    }

    public function getPurchasableItemData(): PurchasableItemData
    {
        return $this->data;
    }

    public function setPurchasableItemData(PurchasableItemData $data): self
    {
        $this->data = $data;

        return $this;
    }
}