<?php

namespace App\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\CampDate;
use Symfony\Contracts\EventDispatcher\Event;

class CampDateUpdateEvent extends Event
{
    public const NAME = 'model.admin.camp_date.update';

    private CampDateData $data;

    private CampDate $entity;

    public function __construct(CampDateData $data, CampDate $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCampDateData(): CampDateData
    {
        return $this->data;
    }

    public function setCampDateData(CampDateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampDate(): CampDate
    {
        return $this->entity;
    }

    public function setCampDate(CampDate $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}