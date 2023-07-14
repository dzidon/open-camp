<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\PermissionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Admin permission used for authorization.
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $name;

    #[ORM\Column(length: 64)]
    private string $label;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: PermissionGroup::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PermissionGroup $group;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $label, int $priority, PermissionGroup $group)
    {
        $this->name = $name;
        $this->label = $label;
        $this->priority = $priority;
        $this->group = $group;
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPermissionGroup(): PermissionGroup
    {
        return $this->group;
    }

    public function setPermissionGroup(PermissionGroup $group): self
    {
        $this->group = $group;

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