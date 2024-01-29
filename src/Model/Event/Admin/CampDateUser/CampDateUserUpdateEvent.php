<?php

namespace App\Model\Event\Admin\CampDateUser;

use App\Library\Data\Admin\CampDateUserData;
use App\Model\Entity\CampDateUser;
use App\Model\Event\AbstractModelEvent;

class CampDateUserUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_user.update';

    private CampDateUserData $data;

    private CampDateUser $entity;

    public function __construct(CampDateUserData $data, CampDateUser $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCampDateUserData(): CampDateUserData
    {
        return $this->data;
    }

    public function setCampDateUserData(CampDateUserData $data): self
    {
        $this->data = $data;

        return $this;
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