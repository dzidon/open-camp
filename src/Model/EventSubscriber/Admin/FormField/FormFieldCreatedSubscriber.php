<?php

namespace App\Model\EventSubscriber\Admin\FormField;

use App\Model\Event\Admin\FormField\FormFieldCreatedEvent;
use App\Model\Repository\FormFieldRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class FormFieldCreatedSubscriber
{
    private FormFieldRepositoryInterface $repository;

    public function __construct(FormFieldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: FormFieldCreatedEvent::NAME)]
    public function onCreatedSaveEntity(FormFieldCreatedEvent $event): void
    {
        $entity = $event->getFormField();
        $this->repository->saveFormField($entity, true);
    }
}