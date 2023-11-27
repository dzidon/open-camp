<?php

namespace App\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use Symfony\Contracts\EventDispatcher\Event;

class FormFieldUpdateEvent extends Event
{
    public const NAME = 'model.admin.form_field.update';

    private FormFieldData $data;

    private FormField $entity;

    public function __construct(FormFieldData $data, FormField $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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