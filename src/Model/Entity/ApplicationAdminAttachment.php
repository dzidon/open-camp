<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\ApplicationAdminAttachmentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * File attached to an application by an administrator.
 */
#[ORM\Entity(repositoryClass: ApplicationAdminAttachmentRepository::class)]
class ApplicationAdminAttachment
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column(length: 8)]
    private string $extension;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'applicationAdminAttachments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $label, string $extension, Application $application)
    {
        $this->id = Uuid::v4();
        $this->label = $label;
        $this->extension = $extension;
        $this->application = $application;
        $this->createdAt = new DateTimeImmutable('now');

        $this->application->addApplicationAdminAttachment($this);
    }

    public function getId(): UuidV4
    {
        return $this->id;
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

    public function getFileName(): string
    {
        return $this->id->toRfc4122() . '.' . $this->extension;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getApplication(): Application
    {
        return $this->application;
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