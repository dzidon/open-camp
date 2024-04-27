<?php

namespace App\Model\EventSubscriber\Admin\Page;

use App\Model\Event\Admin\Page\PageUpdateEvent;
use App\Model\Repository\PageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PageUpdateSubscriber
{
    private PageRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(PageRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: PageUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(PageUpdateEvent $event): void
    {
        $data = $event->getPageData();
        $entity = $event->getPage();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: PageUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(PageUpdateEvent $event): void
    {
        $entity = $event->getPage();
        $isFlush = $event->isFlush();
        $this->repository->savePage($entity, $isFlush);
    }
}