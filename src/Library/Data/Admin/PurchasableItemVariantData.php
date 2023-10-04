<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniquePurchasableItemVariant;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePurchasableItemVariant]
class PurchasableItemVariantData
{
    private ?PurchasableItemVariant $purchasableItemVariant;

    private PurchasableItem $purchasableItem;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function __construct(PurchasableItem $purchasableItem, ?PurchasableItemVariant $purchasableItemVariant = null)
    {
        $this->purchasableItemVariant = $purchasableItemVariant;
        $this->purchasableItem = $purchasableItem;
    }

    public function getPurchasableItemVariant(): ?PurchasableItemVariant
    {
        return $this->purchasableItemVariant;
    }

    public function getPurchasableItem(): PurchasableItem
    {
        return $this->purchasableItem;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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