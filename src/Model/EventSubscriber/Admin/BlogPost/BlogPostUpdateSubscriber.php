<?php

namespace App\Model\EventSubscriber\Admin\BlogPost;

use App\Model\Event\Admin\BlogPost\BlogPostUpdateEvent;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BlogPostUpdateSubscriber
{
    private BlogPostRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(BlogPostRepositoryInterface $repository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: BlogPostUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(BlogPostUpdateEvent $event): void
    {
        $data = $event->getBlogPostData();
        $entity = $event->getBlogPost();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: BlogPostUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(BlogPostUpdateEvent $event): void
    {
        $entity = $event->getBlogPost();
        $isFlush = $event->isFlush();
        $this->repository->saveBlogPost($entity, $isFlush);
    }
}