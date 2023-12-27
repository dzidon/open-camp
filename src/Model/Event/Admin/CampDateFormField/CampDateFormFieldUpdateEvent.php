<?php

namespace App\Model\Event\Admin\CampDateFormField;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\CampDateFormField;
use App\Model\Event\AbstractModelEvent;

class CampDateFormFieldUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_form_field.update';

    private CampDateFormFieldData $data;

    private CampDateFormField $entity;

    public function __construct(CampDateFormFieldData $data, CampDateFormField $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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

    public function getCampDateFormField(): CampDateFormField
    {
        return $this->entity;
    }

    public function setCampDateFormField(CampDateFormField $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}