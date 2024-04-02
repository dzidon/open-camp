<?php

namespace App\Model\Event\Admin\ApplicationFormFieldValue;

use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationFormFieldValue;
use App\Model\Event\AbstractModelEvent;
use LogicException;

class ApplicationFormFieldValueCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_form_field_value.create';

    private ApplicationFormFieldValueData $data;

    private ?Application $application;

    private ?ApplicationCamper $applicationCamper;

    private ?ApplicationFormFieldValue $applicationFormFieldValue = null;

    public function __construct(ApplicationFormFieldValueData $data, ?Application $application, ?ApplicationCamper $applicationCamper)
    {
        $this->data = $data;
        $this->setApplicationAndApplicationCamper($application, $applicationCamper);
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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getApplicationCamper(): ?ApplicationCamper
    {
        return $this->applicationCamper;
    }

    public function setApplicationAndApplicationCamper(?Application $application, ?ApplicationCamper $applicationCamper): self
    {
        $this->application = $application;
        $this->applicationCamper = $applicationCamper;
        $this->assertApplicationAndApplicationCamper();

        return $this;
    }

    public function getApplicationFormFieldValue(): ?ApplicationFormFieldValue
    {
        return $this->applicationFormFieldValue;
    }

    public function setApplicationFormFieldValue(?ApplicationFormFieldValue $applicationFormFieldValue): self
    {
        $this->applicationFormFieldValue = $applicationFormFieldValue;

        return $this;
    }

    private function assertApplicationAndApplicationCamper(): void
    {
        if ($this->application === null && $this->applicationCamper === null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to null.', self::class)
            );
        }

        if ($this->application !== null && $this->applicationCamper !== null)
        {
            throw new LogicException(
                sprintf('%s cannot have $application and $applicationCamper both set to not null values.', self::class)
            );
        }
    }
}