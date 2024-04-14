<?php

namespace App\Model\EventSubscriber\User\BlogPostView;

use App\Model\Entity\BlogPostView;
use App\Model\Event\User\BlogPostView\BlogPostViewCreateEvent;
use App\Model\Repository\BlogPostViewRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BlogPostViewCreateSubscriber
{
    private BlogPostViewRepositoryInterface $applicationContactRepository;

    public function __construct(BlogPostViewRepositoryInterface $applicationContactRepository)
    {
        $this->applicationContactRepository = $applicationContactRepository;
    }

    #[AsEventListener(event: BlogPostViewCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntity(BlogPostViewCreateEvent $event): void
    {
        $blogPost = $event->getBlogPost();
        $visitorId = $event->getVisitorId();
        $blogPostView = new BlogPostView($blogPost, $visitorId);
        $event->setBlogPostView($blogPostView);
    }

    #[AsEventListener(event: BlogPostViewCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(BlogPostViewCreateEvent $event): void
    {
        $blogPostView = $event->getBlogPostView();
        $isFlush = $event->isFlush();
        $this->applicationContactRepository->saveBlogPostView($blogPostView, $isFlush);
    }
}