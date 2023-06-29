<?php

namespace App\Entity;

use App\Repository\PermissionGroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Group for admin permissions.
 */
#[ORM\Entity(repositoryClass: PermissionGroupRepository::class)]
class PermissionGroup
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

    public function __construct(string $name, string $label, int $priority)
    {
        $this->name = $name;
        $this->label = $label;
        $this->priority = $priority;
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
}