<?php

namespace App\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathData;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\AbstractModelEvent;

class TripLocationPathUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.trip_location_path.update';

    private TripLocationPathData $data;

    private TripLocationPath $entity;

    public function __construct(TripLocationPathData $data, TripLocationPath $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getTripLocationPathData(): TripLocationPathData
    {
        return $this->data;
    }

    public function setTripLocationPathData(TripLocationPathData $data): self
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
}