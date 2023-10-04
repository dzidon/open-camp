<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniquePurchasableItemVariantValue;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePurchasableItemVariantValue]
class PurchasableItemVariantValueData
{
    private ?PurchasableItemVariantValue $purchasableItemVariantValue;

    private ?PurchasableItemVariant $purchasableItemVariant;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function __construct(?PurchasableItemVariantValue $purchasableItemVariantValue = null, ?PurchasableItemVariant $purchasableItemVariant = null)
    {
        $this->purchasableItemVariantValue = $purchasableItemVariantValue;
        $this->purchasableItemVariant = $purchasableItemVariant;
    }

    public function getPurchasableItemVariantValue(): ?PurchasableItemVariantValue
    {
        return $this->purchasableItemVariantValue;
    }

    public function getPurchasableItemVariant(): ?PurchasableItemVariant
    {
        return $this->purchasableItemVariant;
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