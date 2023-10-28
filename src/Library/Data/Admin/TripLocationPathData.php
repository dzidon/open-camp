<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueTripLocationPath;
use App\Model\Entity\TripLocationPath;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueTripLocationPath]
class TripLocationPathData
{
    private ?TripLocationPath $tripLocationPath;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    public function __construct(?TripLocationPath $tripLocationPath = null)
    {
        $this->tripLocationPath = $tripLocationPath;
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
}