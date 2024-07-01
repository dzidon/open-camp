<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationAttachmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;

/**
 * File attached to an application.
 */
#[ORM\Entity(repositoryClass: ApplicationAttachmentRepository::class)]
class ApplicationAttachment
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $help;

    #[ORM\Column(type: Types::FLOAT)]
    private float $maxSize; // MB

    #[ORM\Column(length: 16)]
    private string $requiredType;

    #[ORM\Column(type: Types::JSON)]
    private array $extensions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $extension = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationAttachments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Application $application;

    #[ORM\ManyToOne(targetEntity: ApplicationCamper::class, inversedBy: 'applicationAttachments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?ApplicationCamper $applicationCamper;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string                           $label,
                                ?string                          $help,
                                float                            $maxSize,
                                AttachmentConfigRequiredTypeEnum $requiredType,
                                array                            $extensions,
                                int                              $priority,
                                ?Application                     $application,
                                ?ApplicationCamper               $applicationCamper)
    {
        $this->application = $application;
        $this->applicationCamper = $applicationCamper;
        $this->assertApplicationAndApplicationCamper();

        $this->application?->addApplicationAttachment($this);
        $this->applicationCamper?->addApplicationAttachment($this);

        $this->id = Uuid::v4();
        $this->label = $label;
        $this->help = $help;
        $this->maxSize = $maxSize;
        $this->requiredType = $requiredType->value;
        $this->createdAt = new DateTimeImmutable('now');
        $this->extensions = $extensions;
        $this->priority = $priority;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isAlreadyUploaded(): bool
    {
        return $this->extension !== null;
    }

    public function getFileName(): ?string
    {
        if ($this->extension === null)
        {
            return null;
        }

        return $this->id->toRfc4122() . '.' . $this->extension;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getMaxSize(): float
    {
        return $this->maxSize;
    }

    public function getRequiredType(): AttachmentConfigRequiredTypeEnum
    {
        return AttachmentConfigRequiredTypeEnum::tryFrom($this->requiredType);
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function isExpectingLaterUpload(): bool
    {
        return !$this->isAlreadyUploaded() && $this->getRequiredType() === AttachmentConfigRequiredTypeEnum::REQUIRED_LATER;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertApplicationAndApplicationCamper(): void
    {
        if ($this->application === null && $this->applicationCamper === null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to null.', self::class)
            );
        }

        if ($this->application !== null && $this->applicationCamper !== null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to not null values.', self::class)
            );
        }
    }
}