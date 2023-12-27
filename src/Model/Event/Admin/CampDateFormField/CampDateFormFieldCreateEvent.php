<?php

namespace App\Model\Event\Admin\CampDateFormField;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateFormField;
use App\Model\Event\AbstractModelEvent;

class CampDateFormFieldCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_form_field.create';

    private CampDateFormFieldData $data;

    private CampDate $campDate;

    private ?CampDateFormField $entity = null;

    public function __construct(CampDateFormFieldData $data, CampDate $campDate)
    {
        $this->data = $data;
        $this->campDate = $campDate;
    }

    public function getCampDateFormFieldData(): CampDateFormFieldData
    {
        return $this->data;
    }

    public function setCampDateFormFieldData(CampDateFormFieldData $data): self
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

    public function getCampDateFormField(): ?CampDateFormField
    {
        return $this->entity;
    }

    public function setCampDateFormField(?CampDateFormField $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}