<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueTripLocations;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueTripLocations]
class TripLocationPathCreationData
{
    #[Assert\Valid]
    private TripLocationPathData $tripLocationPathData;

    /** @var TripLocationData[] */
    #[Assert\Valid]
    private array $tripLocationsData = [];

    public function __construct()
    {
        $this->tripLocationPathData = new TripLocationPathData();
    }

    public function getTripLocationPathData(): TripLocationPathData
    {
        return $this->tripLocationPathData;
    }

    public function getTripLocationsData(): array
    {
        return $this->tripLocationsData;
    }

    public function addTripLocationsDatum(TripLocationData $tripLocationData): self
    {
        if (in_array($tripLocationData, $this->tripLocationsData, true))
        {
            return $this;
        }

        $this->tripLocationsData[] = $tripLocationData;

        return $this;
    }

    public function removeTripLocationsDatum(TripLocationData $tripLocationData): self
    {
        $key = array_search($tripLocationData, $this->tripLocationsData, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->tripLocationsData[$key]);

        return $this;
    }
}