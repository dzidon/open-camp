<?php

namespace App\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use App\Model\Event\AbstractModelEvent;

class TripLocationUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.trip_location.update';

    private TripLocationData $data;

    private TripLocation $entity;

    public function __construct(TripLocationData $data, TripLocation $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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

    public function getTripLocation(): TripLocation
    {
        return $this->entity;
    }

    public function setTripLocation(TripLocation $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}