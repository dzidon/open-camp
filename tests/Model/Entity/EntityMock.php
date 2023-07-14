<?php

namespace App\Tests\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use DateTimeImmutable;

class EntityMock
{
    private string $name;

    private string $label;

    private DateTimeImmutable $createdAt;

    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
        $this->createdAt = new DateTimeImmutable('now');
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}