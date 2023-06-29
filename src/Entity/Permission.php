<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
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

    public function __construct(string $name, string $label, int $priority, PermissionGroup $group)
    {
        $this->name = $name;
        $this->label = $label;
        $this->priority = $priority;
        $this->group = $group;
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
}