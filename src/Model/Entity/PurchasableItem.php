<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PurchasableItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Purchasable item config that can be attached to a camp application.
 */
#[ORM\Entity(repositoryClass: PurchasableItemRepository::class)]
class PurchasableItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: Types::FLOAT)]
    private float $price;

    #[ORM\Column(type: Types::INTEGER)]
    private int $maxAmount;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isGlobal = false;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $imageExtension = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'purchasableItem', targetEntity: PurchasableItemVariant::class)]
    private Collection $purchasableItemVariants;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $label, float $price, int $maxAmount)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->label = $label;
        $this->price = $price;
        $this->maxAmount = $maxAmount;
        $this->purchasableItemVariants = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(int $maxAmount): self
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

    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    public function setImageExtension(?string $imageExtension): self
    {
        $this->imageExtension = $imageExtension;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return PurchasableItemVariant[]
     */
    public function getPurchasableItemVariants(): array
    {
        return $this->purchasableItemVariants->toArray();
    }

    /**
     * @internal Inverse side.
     * @param PurchasableItemVariant $purchasableItemVariant
     * @return $this
     */
    public function addPurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant): self
    {
        if (!$this->purchasableItemVariants->contains($purchasableItemVariant))
        {
            $this->purchasableItemVariants->add($purchasableItemVariant);
            $purchasableItemVariant->setPurchasableItem($this);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param PurchasableItemVariant $purchasableItemVariant
     * @return $this
     */
    public function removePurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant): self
    {
        $this->purchasableItemVariants->removeElement($purchasableItemVariant);

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}