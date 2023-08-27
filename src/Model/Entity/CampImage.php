<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CampImageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp image.
 */
#[ORM\Entity(repositoryClass: CampImageRepository::class)]
class CampImage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\Column(length: 8)]
    private string $extension;

    #[ORM\ManyToOne(targetEntity: Camp::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Camp $camp;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(int $priority, string $extension, Camp $camp)
    {
        $this->id = Uuid::v4();
        $this->priority = $priority;
        $this->extension = $extension;
        $this->camp = $camp;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
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

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function setCamp(Camp $camp): self
    {
        $this->camp = $camp;

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