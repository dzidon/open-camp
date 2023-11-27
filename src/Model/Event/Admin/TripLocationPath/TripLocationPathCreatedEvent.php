<?php

namespace App\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Model\Entity\TripLocationPath;
use App\Model\Entity\TripLocation;
use LogicException;
use Symfony\Contracts\EventDispatcher\Event;

class TripLocationPathCreatedEvent extends Event
{
    public const NAME = 'model.admin.trip_location_path.created';

    private TripLocationPathCreationData $data;

    private TripLocationPath $entity;

    /** @var TripLocation[] */
    private array $locations;

    public function __construct(TripLocationPathCreationData $data, TripLocationPath $entity, array $locations)
    {
        foreach ($locations as $location)
        {
            if (!$location instanceof TripLocation)
            {
                throw new LogicException(
                    sprintf("Locations passed to the constructor of %s must all be instances of %s.", self::class, TripLocation::class)
                );
            }
        }

        $this->data = $data;
        $this->entity = $entity;
        $this->locations = $locations;
    }

    public function getTripLocationPathCreationData(): TripLocationPathCreationData
    {
        return $this->data;
    }

    public function setTripLocationPathCreationData(TripLocationPathCreationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTripLocationPath(): TripLocationPath
    {
        return $this->entity;
    }

    public function setTripLocationPath(TripLocationPath $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getTripLocations(): array
    {
        return $this->locations;
    }

    public function addTripLocation(TripLocation $tripLocation): self
    {
        if (in_array($tripLocation, $this->locations, true))
        {
            return $this;
        }

        $this->locations[] = $tripLocation;

        return $this;
    }

    public function removeTripLocation(TripLocation $tripLocation): self
    {
        $key = array_search($tripLocation, $this->locations, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->locations[$key]);

        return $this;
    }
}