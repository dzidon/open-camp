<?php

namespace App\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\AbstractModelEvent;

class TripLocationCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.trip_location.create';

    private TripLocationData $data;

    private TripLocationPath $tripLocationPath;

    private ?TripLocation $tripLocation = null;

    public function __construct(TripLocationData $data, TripLocationPath $tripLocationPath)
    {
        $this->data = $data;
        $this->tripLocationPath = $tripLocationPath;
    }

    public function getTripLocationData(): TripLocationData
    {
        return $this->data;
    }

    public function setTripLocationData(TripLocationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTripLocationPath(): TripLocationPath
    {
        return $this->tripLocationPath;
    }

    public function setTripLocationPath(TripLocationPath $tripLocationPath): self
    {
        $this->tripLocationPath = $tripLocationPath;

        return $this;
    }

    public function getTripLocation(): ?TripLocation
    {
        return $this->tripLocation;
    }

    public function setTripLocation(?TripLocation $tripLocation): self
    {
        $this->tripLocation = $tripLocation;

        return $this;
    }
}