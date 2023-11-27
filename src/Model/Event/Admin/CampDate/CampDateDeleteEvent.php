<?php

namespace App\Model\Event\Admin\CampDate;

use App\Model\Entity\CampDate;
use Symfony\Contracts\EventDispatcher\Event;

class CampDateDeleteEvent extends Event
{
    public const NAME = 'model.admin.camp_date.delete';

    private CampDate $entity;

    public function __construct(CampDate $entity)
    {
        $this->entity = $entity;
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