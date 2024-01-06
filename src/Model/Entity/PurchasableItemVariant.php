<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PurchasableItemVariantRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Variant attached to purchasable item.
 */
#[ORM\Entity(repositoryClass: PurchasableItemVariantRepository::class)]
class PurchasableItemVariant
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: PurchasableItem::class, inversedBy: 'purchasableItemVariants')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PurchasableItem $purchasableItem;

    #[ORM\OneToMany(mappedBy: 'purchasableItemVariant', targetEntity: PurchasableItemVariantValue::class)]
    private Collection $purchasableItemVariantValues;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, int $priority, PurchasableItem $purchasableItem)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->priority = $priority;
        $this->purchasableItemVariantValues = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
        $this->setPurchasableItem($purchasableItem);
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPurchasableItem(): PurchasableItem
    {
        return $this->purchasableItem;
    }

    public function setPurchasableItem(PurchasableItem $purchasableItem): self
    {
        if (isset($this->purchasableItem))
        {
            if ($this->purchasableItem === $purchasableItem)
            {
                return $this;
            }

            $this->purchasableItem->removePurchasableItemVariant($this);
        }

        $this->purchasableItem = $purchasableItem;
        $this->purchasableItem->addPurchasableItemVariant($this);

        return $this;
    }

    /**
     * @return PurchasableItemVariantValue[]
     */
    public function getPurchasableItemVariantValues(): array
    {
        return $this->purchasableItemVariantValues->toArray();
    }

    /**
     * @internal Inverse side.
     * @param PurchasableItemVariantValue $purchasableItemVariantValue
     * @return $this
     */
    public function addPurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue): self
    {
        if (!$this->purchasableItemVariantValues->contains($purchasableItemVariantValue))
        {
            $this->purchasableItemVariantValues->add($purchasableItemVariantValue);
            $purchasableItemVariantValue->setPurchasableItemVariant($this);
        }

        return $this;
    }

    /**
     * @internal Inverse side.
     * @param PurchasableItemVariantValue $purchasableItemVariantValue
     * @return $this
     */
    public function removePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue): self
    {
        $this->purchasableItemVariantValues->removeElement($purchasableItemVariantValue);

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