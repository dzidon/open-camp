<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueElementsInArray;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class TripLocationPathCreationData
{
    #[Assert\Valid]
    private TripLocationPathData $tripLocationPathData;

    /** @var TripLocationData[] */
    #[Assert\Valid]
    #[UniqueElementsInArray(fields: ['name'], message: 'unique_trip_locations')]
    #[Assert\NotBlank(message: 'trip_locations_mandatory')]
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

    public function setTripLocationsData(array $tripLocationsData): self
    {
        foreach ($tripLocationsData as $tripLocationData)
        {
            if (!$tripLocationData instanceof TripLocationData)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, TripLocationData::class)
                );
            }
        }

        $this->tripLocationsData = $tripLocationsData;

        return $this;
    }

    public function addTripLocationData(TripLocationData $tripLocationData): self
    {
        if (in_array($tripLocationData, $this->tripLocationsData, true))
        {
            return $this;
        }

        $this->tripLocationsData[] = $tripLocationData;

        return $this;
    }

    public function removeTripLocationData(TripLocationData $tripLocationData): self
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