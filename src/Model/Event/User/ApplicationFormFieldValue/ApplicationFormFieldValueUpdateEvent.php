<?php

namespace App\Model\Event\User\ApplicationFormFieldValue;

use App\Library\Data\User\ApplicationFormFieldValueData;
use App\Model\Entity\ApplicationFormFieldValue;
use App\Model\Event\AbstractModelEvent;

class ApplicationFormFieldValueUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_form_field_value.update';

    private ApplicationFormFieldValueData $data;

    private ApplicationFormFieldValue $applicationFormFieldValue;

    public function __construct(ApplicationFormFieldValueData $data, ApplicationFormFieldValue $applicationFormFieldValue)
    {
        $this->data = $data;
        $this->applicationFormFieldValue = $applicationFormFieldValue;
    }

    public function getApplicationFormFieldValueData(): ApplicationFormFieldValueData
    {
        return $this->data;
    }

    public function setApplicationFormFieldValueData(ApplicationFormFieldValueData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApplicationFormFieldValue(): ApplicationFormFieldValue
    {
        return $this->applicationFormFieldValue;
    }

    public function setApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue): self
    {
        $this->applicationFormFieldValue = $applicationFormFieldValue;

        return $this;
    }
}