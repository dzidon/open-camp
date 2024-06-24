<?php

namespace App\Model\EventSubscriber\User\BlogPost;

use App\Model\Event\User\BlogPost\BlogPostReadEvent;
use App\Model\Event\User\BlogPostView\BlogPostViewCreateEvent;
use App\Model\Repository\BlogPostViewRepositoryInterface;
use App\Service\Visitor\VisitorIdHttpStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlogPostReadSubscriber
{
    private VisitorIdHttpStorageInterface $visitorIdHttpStorage;

    private BlogPostViewRepositoryInterface $blogPostViewRepository;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(VisitorIdHttpStorageInterface   $visitorIdProvider,
                                BlogPostViewRepositoryInterface $blogPostViewRepository,
                                EventDispatcherInterface        $eventDispatcher)
    {
        $this->visitorIdHttpStorage = $visitorIdProvider;
        $this->blogPostViewRepository = $blogPostViewRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: BlogPostReadEvent::NAME)]
    public function onReadIncrementViewCount(BlogPostReadEvent $event): void
    {
        $blogPost = $event->getBlogPost();
        $visitorId = $this->visitorIdHttpStorage->getCurrentVisitorId();

        if ($visitorId === null)
        {
            $visitorId = $this->visitorIdHttpStorage->getNewVisitorId();
        }

        if (!$this->blogPostViewRepository->hasVisitorSeenBlogPost($blogPost, $visitorId))
        {
            $isFlush = $event->isFlush();
            $newEvent = new BlogPostViewCreateEvent($blogPost, $visitorId);
            $newEvent->setIsFlush($isFlush);
            $this->eventDispatcher->dispatch($newEvent, $newEvent::NAME);
        }
    }
}