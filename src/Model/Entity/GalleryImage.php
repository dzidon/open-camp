<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\GalleryImageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gallery image category.
 */
#[ORM\Entity(repositoryClass: GalleryImageRepository::class)]
class GalleryImage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 8)]
    private string $extension;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isHiddenInGallery = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isInCarousel = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $carouselPriority = null;

    #[ORM\ManyToOne(targetEntity: GalleryImageCategory::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?GalleryImageCategory $galleryImageCategory = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $extension)
    {
        $this->id = Uuid::v4();
        $this->extension = $extension;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFileName(): string
    {
        return $this->id->toRfc4122() . '.' . $this->extension;
    }

    public function isHiddenInGallery(): bool
    {
        return $this->isHiddenInGallery;
    }

    public function setIsHiddenInGallery(bool $isHiddenInGallery): self
    {
        $this->isHiddenInGallery = $isHiddenInGallery;

        return $this;
    }

    public function isInCarousel(): bool
    {
        return $this->isInCarousel;
    }

    public function setIsInCarousel(bool $isInCarousel): self
    {
        $this->isInCarousel = $isInCarousel;

        return $this;
    }

    public function getCarouselPriority(): ?int
    {
        return $this->carouselPriority;
    }

    public function setCarouselPriority(?int $carouselPriority): self
    {
        $this->carouselPriority = $carouselPriority;

        return $this;
    }

    public function getGalleryImageCategory(): ?GalleryImageCategory
    {
        return $this->galleryImageCategory;
    }

    public function setGalleryImageCategory(?GalleryImageCategory $galleryImageCategory): self
    {
        $this->galleryImageCategory = $galleryImageCategory;

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