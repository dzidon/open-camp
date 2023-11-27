<?php

namespace App\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use App\Model\Entity\TripLocation;
use Symfony\Contracts\EventDispatcher\Event;

class TripLocationCreatedEvent extends Event
{
    public const NAME = 'model.admin.trip_location.created';

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