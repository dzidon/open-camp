<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PurchasableItemRepository;
use DateTimeImmutable;
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

    #[ORM\Column(type: Types::FLOAT)]
    private float $price;

    #[ORM\Column(type: Types::INTEGER)]
    private int $maxAmountPerCamper;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, float $price, int $maxAmountPerCamper)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->price = $price;
        $this->maxAmountPerCamper = $maxAmountPerCamper;
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxAmountPerCamper(): int
    {
        return $this->maxAmountPerCamper;
    }

    public function setMaxAmountPerCamper(int $maxAmountPerCamper): self
    {
        $this->maxAmountPerCamper = $maxAmountPerCamper;

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