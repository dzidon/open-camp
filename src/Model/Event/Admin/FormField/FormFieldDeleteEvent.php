<?php

namespace App\Model\Event\Admin\FormField;

use App\Model\Entity\FormField;
use App\Model\Event\AbstractModelEvent;

class FormFieldDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.form_field.delete';

    private FormField $entity;

    public function __construct(FormField $entity)
    {
        $this->entity = $entity;
    }

    public function getFormField(): FormField
    {
        return $this->entity;
    }

    public function setFormField(FormField $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}