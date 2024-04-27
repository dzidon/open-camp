<?php

namespace App\Model\EventSubscriber\Admin\Page;

use App\Model\Entity\Page;
use App\Model\Event\Admin\Page\PageCreateEvent;
use App\Model\Repository\PageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class PageCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private PageRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, PageRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: PageCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(PageCreateEvent $event): void
    {
        $data = $event->getPageData();
        $entity = new Page(
            $data->getTitle(),
            $data->getUrlName(),
            $data->getContent()
        );

        $this->dataTransfer->fillEntity($data, $entity);
        $event->setPage($entity);
    }

    #[AsEventListener(event: PageCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(PageCreateEvent $event): void
    {
        $entity = $event->getPage();
        $isFlush = $event->isFlush();
        $this->repository->savePage($entity, $isFlush);
    }
}