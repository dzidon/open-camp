<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\CampDateAttachmentConfigRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Many to many connection between camp dates and attachment configs.
 */
#[ORM\Entity(repositoryClass: CampDateAttachmentConfigRepository::class)]
class CampDateAttachmentConfig
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\ManyToOne(targetEntity: CampDate::class, inversedBy: 'campDateAttachmentConfigs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CampDate $campDate;

    #[ORM\ManyToOne(targetEntity: AttachmentConfig::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AttachmentConfig $attachmentConfig;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(CampDate $campDate, AttachmentConfig $attachmentConfig, int $priority)
    {
        $this->id = Uuid::v4();
        $this->attachmentConfig = $attachmentConfig;
        $this->priority = $priority;
        $this->createdAt = new DateTimeImmutable('now');
        $this->setCampDate($campDate);
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getCampDate(): CampDate
    {
        return $this->campDate;
    }

    public function setCampDate(CampDate $campDate): self
    {
        if (isset($this->campDate))
        {
            if ($this->campDate === $campDate)
            {
                return $this;
            }

            $this->campDate->removeCampDateAttachmentConfig($this);
        }

        $this->campDate = $campDate;
        $this->campDate->addCampDateAttachmentConfig($this);

        return $this;
    }

    public function getAttachmentConfig(): AttachmentConfig
    {
        return $this->attachmentConfig;
    }

    public function setAttachmentConfig(AttachmentConfig $attachmentConfig): self
    {
        $this->attachmentConfig = $attachmentConfig;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}