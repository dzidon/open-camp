<?php

namespace App\Model\Event\User\Application;

use App\Model\Entity\Application;
use App\Model\Event\AbstractModelEvent;
use Symfony\Component\HttpFoundation\Response;

class ApplicationDraftStoreInHttpStorageEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application.draft_store_in_http_storage';

    private Application $application;

    private Response $response;

    public function __construct(Application $application, Response $response)
    {
        $this->application = $application;
        $this->response = $response;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}