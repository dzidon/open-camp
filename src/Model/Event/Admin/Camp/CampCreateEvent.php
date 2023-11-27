<?php

namespace App\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use Symfony\Contracts\EventDispatcher\Event;

class CampCreateEvent extends Event
{
    public const NAME = 'model.admin.camp.create';

    private CampCreationData $data;

    public function __construct(CampCreationData $data)
    {
        $this->data = $data;
    }

    public function getCampCreationData(): CampCreationData
    {
        return $this->data;
    }

    public function setCampCreationData(CampCreationData $data): self
    {
        $this->data = $data;

        return $this;
    }
}