<?php

namespace App\Model\Event\Admin\TripLocation;

use App\Library\Data\Admin\TripLocationData;
use Symfony\Contracts\EventDispatcher\Event;

class TripLocationCreateEvent extends Event
{
    public const NAME = 'model.admin.trip_location.create';

    private TripLocationData $data;

    public function __construct(TripLocationData $data)
    {
        $this->data = $data;
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
}