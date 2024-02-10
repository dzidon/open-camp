<?php

namespace App\Model\Event\User\Application;

use App\Library\Data\User\ApplicationImportToUserData;
use App\Model\Event\AbstractModelEvent;

class ApplicationToUserImportEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.import';

    private ApplicationImportToUserData $data;

    public function __construct(ApplicationImportToUserData $data)
    {
        $this->data = $data;
    }

    public function getApplicationImportToUserData(): ApplicationImportToUserData
    {
        return $this->data;
    }

    public function setApplicationImportToUserData(ApplicationImportToUserData $data): self
    {
        $this->data = $data;

        return $this;
    }
}