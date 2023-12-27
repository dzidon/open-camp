<?php

namespace App\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampCreationData;
use App\Model\Entity\Camp;
use App\Model\Event\AbstractModelEvent;

class CampCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp.create';

    private CampCreationData $data;

    private ?Camp $camp = null;

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

    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): self
    {
        $this->camp = $camp;

        return $this;
    }
}