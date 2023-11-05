<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\PurchasableItem;
use Symfony\Component\Validator\Constraints as Assert;

class CampDatePurchasableItemData
{
    #[Assert\NotBlank]
    private ?PurchasableItem $purchasableItem = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function getPurchasableItem(): ?PurchasableItem
    {
        return $this->purchasableItem;
    }

    public function setPurchasableItem(?PurchasableItem $purchasableItem): self
    {
        $this->purchasableItem = $purchasableItem;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}