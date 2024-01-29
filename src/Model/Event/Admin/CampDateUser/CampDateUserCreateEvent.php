<?php

namespace App\Model\Event\Admin\CampDateUser;

use App\Library\Data\Admin\CampDateUserData;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateUser;
use App\Model\Event\AbstractModelEvent;

class CampDateUserCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_user.create';

    private CampDateUserData $data;

    private CampDate $campDate;

    private ?CampDateUser $entity = null;

    public function __construct(CampDateUserData $data, CampDate $campDate)
    {
        $this->data = $data;
        $this->campDate = $campDate;
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

    public function getCampDate(): CampDate
    {
        return $this->campDate;
    }

    public function setCampDate(CampDate $campDate): self
    {
        $this->campDate = $campDate;

        return $this;
    }

    public function getCampDateUser(): ?CampDateUser
    {
        return $this->entity;
    }

    public function setCampDateUser(?CampDateUser $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}