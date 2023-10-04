<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniquePurchasableItem;
use App\Model\Entity\PurchasableItem;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePurchasableItem]
class PurchasableItemData
{
    private ?PurchasableItem $purchasableItem;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotBlank]
    private ?int $maxAmountPerCamper = null;

    public function __construct(?PurchasableItem $purchasableItem = null)
    {
        $this->purchasableItem = $purchasableItem;
    }

    public function getPurchasableItem(): ?PurchasableItem
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxAmountPerCamper(): ?int
    {
        return $this->maxAmountPerCamper;
    }

    public function setMaxAmountPerCamper(?int $maxAmountPerCamper): self
    {
        $this->maxAmountPerCamper = $maxAmountPerCamper;

        return $this;
    }
}