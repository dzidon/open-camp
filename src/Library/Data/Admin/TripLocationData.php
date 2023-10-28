<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueTripLocation;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueTripLocation]
class TripLocationData
{
    private ?TripLocation $tripLocation;

    private ?TripLocationPath $tripLocationPath;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\GreaterThanOrEqual(0.0)]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    public function __construct(?TripLocation $tripLocation = null, ?TripLocationPath $tripLocationPath = null)
    {
        $this->tripLocationPath = $tripLocationPath;
        $this->tripLocation = $tripLocation;
    }

    public function getTripLocation(): ?TripLocation
    {
        return $this->tripLocation;
    }

    public function getTripLocationPath(): ?TripLocationPath
    {
        return $this->tripLocationPath;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}