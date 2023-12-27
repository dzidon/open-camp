<?php

namespace App\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use App\Model\Entity\TripLocationPath;
use App\Model\Event\AbstractModelEvent;

class TripLocationPathCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.trip_location_path.create';

    private TripLocationPathCreationData $data;

    private ?TripLocationPath $entity = null;

    public function __construct(TripLocationPathCreationData $data)
    {
        $this->data = $data;
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

    public function getTripLocationPath(): ?TripLocationPath
    {
        return $this->entity;
    }

    public function setTripLocationPath(?TripLocationPath $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}