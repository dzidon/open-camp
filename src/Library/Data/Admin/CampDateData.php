<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\CampDateInterval;
use App\Model\Entity\Camp;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[CampDateInterval]
class CampDateData
{
    private ?UuidV4 $id = null;

    private ?Camp $camp = null;

    #[Assert\NotBlank]
    private ?DateTimeImmutable $startAt = null;

    #[Assert\GreaterThanOrEqual(propertyPath: 'startAt')]
    #[Assert\NotBlank]
    private ?DateTimeImmutable $endAt = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotBlank]
    private ?int $capacity = null;

    private bool $isClosed = false;

    #[Assert\Length(max: 2000)]
    private ?string $tripInstructions = null;

    /**
     * @var User[]
     */
    private iterable $leaders = [];

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function setId(?UuidV4 $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): self
    {
        $this->camp = $camp;

        return $this;
    }

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function getTripInstructions(): ?string
    {
        return $this->tripInstructions;
    }

    public function setTripInstructions(?string $tripInstructions): self
    {
        $this->tripInstructions = $tripInstructions;

        return $this;
    }

    public function getLeaders(): iterable
    {
        return $this->leaders;
    }

    public function setLeaders(iterable $leaders): self
    {
        $this->leaders = $leaders;

        return $this;
    }
}