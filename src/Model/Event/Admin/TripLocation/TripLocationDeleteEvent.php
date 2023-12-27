<?php

namespace App\Model\Event\Admin\TripLocation;

use App\Model\Entity\TripLocation;
use App\Model\Event\AbstractModelEvent;

class TripLocationDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.trip_location.delete';

    private TripLocation $entity;

    public function __construct(TripLocation $entity)
    {
        $this->entity = $entity;
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