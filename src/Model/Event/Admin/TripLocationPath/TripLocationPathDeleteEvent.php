<?php

namespace App\Model\Event\Admin\TripLocationPath;

use App\Model\Entity\TripLocationPath;
use Symfony\Contracts\EventDispatcher\Event;

class TripLocationPathDeleteEvent extends Event
{
    public const NAME = 'model.admin.trip_location_path.delete';

    private TripLocationPath $entity;

    public function __construct(TripLocationPath $entity)
    {
        $this->entity = $entity;
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
}