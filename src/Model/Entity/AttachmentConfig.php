<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\AttachmentConfigRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;

/**
 * Application file attachment config entity.
 */
#[ORM\Entity(repositoryClass: AttachmentConfigRepository::class)]
class AttachmentConfig
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255, unique: true)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(type: Types::FLOAT)]
    private float $maxSize; // MB

    #[ORM\Column(length: 16)]
    private string $requiredType;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isGlobal = false;

    #[ORM\ManyToMany(targetEntity: FileExtension::class)]
    private Collection $fileExtensions;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $label, float $maxSize)
    {
        $this->id = Uuid::v4();
        $this->name = $name;
        $this->label = $label;
        $this->maxSize = $maxSize;
        $this->requiredType = AttachmentConfigRequiredTypeEnum::OPTIONAL->value;
        $this->fileExtensions = new ArrayCollection();
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

    public function getFileExtensionsAsString(): string
    {
        $extensions = [];

        /** @var FileExtension $fileExtension */
        foreach ($this->fileExtensions as $fileExtension)
        {
            $extensions[] = $fileExtension->getExtension();
        }

        return implode(', ', $extensions);
    }

    /**
     * @return FileExtension[]
     */
    public function getFileExtensions(): array
    {
        return $this->fileExtensions->toArray();
    }

    public function addFileExtension(FileExtension $fileExtension): self
    {
        if (!$this->fileExtensions->contains($fileExtension))
        {
            $this->fileExtensions->add($fileExtension);
        }

        return $this;
    }

    public function removeFileExtension(FileExtension $fileExtension): self
    {
        $this->fileExtensions->removeElement($fileExtension);

        return $this;
    }

    public function getMaxSize(): float
    {
        return $this->maxSize;
    }

    public function setMaxSize(float $maxSize): self
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    public function getRequiredType(): AttachmentConfigRequiredTypeEnum
    {
        return AttachmentConfigRequiredTypeEnum::tryFrom($this->requiredType);
    }

    public function setRequiredType(AttachmentConfigRequiredTypeEnum $requiredType): self
    {
        $this->requiredType = $requiredType->value;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}