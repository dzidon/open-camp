<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\CampDateInterval;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

#[CampDateInterval]
class CampDateData
{
    private ?CampDate $campDate;

    private Camp $camp;

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

    private bool $isOpenAboveCapacity = false;

    private bool $isClosed = false;

    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    /**
     * @var User[]
     */
    private array $leaders = [];

    public function __construct(Camp $camp, ?CampDate $campDate = null)
    {
        $this->camp = $camp;
        $this->campDate = $campDate;
    }

    public function getCampDate(): ?CampDate
    {
        return $this->campDate;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
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

    public function isOpenAboveCapacity(): bool
    {
        return $this->isOpenAboveCapacity;
    }

    public function setIsOpenAboveCapacity(bool $isOpenAboveCapacity): self
    {
        $this->isOpenAboveCapacity = $isOpenAboveCapacity;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLeaders(): array
    {
        return $this->leaders;
    }

    public function addLeader(User $leader): self
    {
        if (in_array($leader, $this->leaders))
        {
            return $this;
        }

        $this->leaders[] = $leader;

        return $this;
    }

    public function removeLeader(User $leader): self
    {
        $key = array_search($leader, $this->leaders, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->leaders[$key]);

        return $this;
    }
}