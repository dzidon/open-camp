<?php

namespace App\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Model\Event\AbstractModelEvent;

class FormFieldCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.form_field.create';

    private FormFieldData $data;

    private ?FormField $entity = null;

    public function __construct(FormFieldData $data)
    {
        $this->data = $data;
    }

    public function getFormFieldData(): FormFieldData
    {
        return $this->data;
    }

    public function setFormFieldData(FormFieldData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getFormField(): ?FormField
    {
        return $this->entity;
    }

    public function setFormField(?FormField $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}