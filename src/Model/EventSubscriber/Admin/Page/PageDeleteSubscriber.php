<?php

namespace App\Model\EventSubscriber\Admin\Page;

use App\Model\Event\Admin\Page\PageDeleteEvent;
use App\Model\Repository\PageRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PageDeleteSubscriber
{
    private PageRepositoryInterface $repository;

    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: PageDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(PageDeleteEvent $event): void
    {
        $entity = $event->getPage();
        $isFlush = $event->isFlush();
        $this->repository->removePage($entity, $isFlush);
    }
}