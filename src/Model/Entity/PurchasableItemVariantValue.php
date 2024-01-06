<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PurchasableItemVariantValueRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Value attached to purchasable item variant.
 */
#[ORM\Entity(repositoryClass: PurchasableItemVariantValueRepository::class)]
class PurchasableItemVariantValue
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: PurchasableItemVariant::class, inversedBy: 'purchasableItemVariantValues')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PurchasableItemVariant $purchasableItemVariant;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, int $priority, PurchasableItemVariant $purchasableItemVariant)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->priority = $priority;
        $this->purchasableItemVariant = $purchasableItemVariant;
        $this->createdAt = new DateTimeImmutable('now');
        $this->setPurchasableItemVariant($purchasableItemVariant);
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

    public function getPurchasableItemVariant(): PurchasableItemVariant
    {
        return $this->purchasableItemVariant;
    }

    public function setPurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant): self
    {
        if (isset($this->purchasableItemVariant))
        {
            if ($this->purchasableItemVariant === $purchasableItemVariant)
            {
                return $this;
            }

            $this->purchasableItemVariant->removePurchasableItemVariantValue($this);
        }

        $this->purchasableItemVariant = $purchasableItemVariant;
        $this->purchasableItemVariant->addPurchasableItemVariantValue($this);

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