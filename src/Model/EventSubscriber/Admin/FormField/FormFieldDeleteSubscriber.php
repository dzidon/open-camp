<?php

namespace App\Model\EventSubscriber\Admin\FormField;

use App\Model\Event\Admin\FormField\FormFieldDeleteEvent;
use App\Model\Repository\FormFieldRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class FormFieldDeleteSubscriber
{
    private FormFieldRepositoryInterface $repository;

    public function __construct(FormFieldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: FormFieldDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(FormFieldDeleteEvent $event): void
    {
        $entity = $event->getFormField();
        $isFlush = $event->isFlush();
        $this->repository->removeFormField($entity, $isFlush);
    }
}