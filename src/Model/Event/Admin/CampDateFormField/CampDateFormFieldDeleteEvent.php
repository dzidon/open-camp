<?php

namespace App\Model\Event\Admin\CampDateFormField;

use App\Model\Entity\CampDateFormField;
use App\Model\Event\AbstractModelEvent;

class CampDateFormFieldDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date_form_field.delete';

    private CampDateFormField $entity;

    public function __construct(CampDateFormField $entity)
    {
        $this->entity = $entity;
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