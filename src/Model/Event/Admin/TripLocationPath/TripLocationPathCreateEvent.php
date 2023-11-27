<?php

namespace App\Model\Event\Admin\TripLocationPath;

use App\Library\Data\Admin\TripLocationPathCreationData;
use Symfony\Contracts\EventDispatcher\Event;

class TripLocationPathCreateEvent extends Event
{
    public const NAME = 'model.admin.trip_location_path.create';

    private TripLocationPathCreationData $data;

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
}