<?php

namespace App\Model\EventSubscriber\Admin\BlogPost;

use App\Model\Event\Admin\BlogPost\BlogPostDeleteEvent;
use App\Model\Repository\BlogPostRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BlogPostDeleteSubscriber
{
    private BlogPostRepositoryInterface $repository;

    public function __construct(BlogPostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: BlogPostDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(BlogPostDeleteEvent $event): void
    {
        $entity = $event->getBlogPost();
        $isFlush = $event->isFlush();
        $this->repository->removeBlogPost($entity, $isFlush);
    }
}