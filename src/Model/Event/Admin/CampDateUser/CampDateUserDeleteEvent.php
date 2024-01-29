<?php

namespace App\Model\Event\Admin\CampDateUser;

use App\Model\Entity\CampDateUser;
use App\Model\Event\AbstractModelEvent;

class CampDateUserDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_user.delete';

    private CampDateUser $entity;

    public function __construct(CampDateUser $entity)
    {
        $this->entity = $entity;
    }

    public function getCampDateUser(): CampDateUser
    {
        return $this->entity;
    }

    public function setCampDateUser(CampDateUser $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}