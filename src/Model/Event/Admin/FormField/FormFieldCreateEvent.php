<?php

namespace App\Model\Event\Admin\FormField;

use App\Library\Data\Admin\FormFieldData;
use Symfony\Contracts\EventDispatcher\Event;

class FormFieldCreateEvent extends Event
{
    public const NAME = 'model.admin.form_field.create';

    private FormFieldData $data;

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
}