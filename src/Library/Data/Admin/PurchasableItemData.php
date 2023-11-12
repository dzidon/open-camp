<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniquePurchasableItem;
use App\Model\Entity\PurchasableItem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[UniquePurchasableItem]
class PurchasableItemData
{
    private ?PurchasableItem $purchasableItem;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotBlank]
    private ?int $maxAmount = null;

    private bool $isGlobal = false;

    #[Assert\Image]
    private ?File $image = null;

    private bool $removeImage = false;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

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

    public function getMaxAmount(): ?int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?int $maxAmount): self
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    public function isGlobal(): bool
    {
        return $this->isGlobal;
    }

    public function setIsGlobal(bool $isGlobal): self
    {
        $this->isGlobal = $isGlobal;

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function removeImage(): bool
    {
        return $this->removeImage;
    }

    public function setRemoveImage(bool $removeImage): self
    {
        $this->removeImage = $removeImage;

        return $this;
    }
}