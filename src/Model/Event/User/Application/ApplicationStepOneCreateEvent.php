<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class ApplicationStepOneCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.step_one_create';

    private ApplicationStepOneData $data;

    private CampDate $campDate;

    private ?User $user;

    private ?Application $application = null;

    public function __construct(ApplicationStepOneData $data, CampDate $campDate, ?User $user = null)
    {
        $this->data = $data;
        $this->campDate = $campDate;
        $this->user = $user;
    }

    public function getApplicationStepOneData(): ApplicationStepOneData
    {
        return $this->data;
    }

    public function setApplicationStepOneData(ApplicationStepOneData $data): self
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}