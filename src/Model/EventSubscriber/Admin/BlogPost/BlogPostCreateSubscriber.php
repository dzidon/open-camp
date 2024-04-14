<?php

namespace App\Model\EventSubscriber\Admin\BlogPost;

use App\Model\Entity\BlogPost;
use App\Model\Event\Admin\BlogPost\BlogPostCreateEvent;
use App\Model\Repository\BlogPostRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BlogPostCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private BlogPostRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, BlogPostRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: BlogPostCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(BlogPostCreateEvent $event): void
    {
        $data = $event->getBlogPostData();
        $author = $event->getAuthor();
        $entity = new BlogPost(
            $data->getTitle(),
            $data->getUrlName(),
            $data->getDescription(),
            $data->getContent(),
            $author
        );

        $this->dataTransfer->fillEntity($data, $entity);
        $event->setBlogPost($entity);
    }

    #[AsEventListener(event: BlogPostCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(BlogPostCreateEvent $event): void
    {
        $entity = $event->getBlogPost();
        $isFlush = $event->isFlush();
        $this->repository->saveBlogPost($entity, $isFlush);
    }
}