<?php

namespace App\Model\Event\Admin\BlogPost;

use App\Library\Data\Admin\BlogPostData;
use App\Model\Entity\BlogPost;
use App\Model\Event\AbstractModelEvent;

class BlogPostUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.blog_post.update';

    private BlogPostData $data;

    private BlogPost $entity;

    public function __construct(BlogPostData $data, BlogPost $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getBlogPostData(): BlogPostData
    {
        return $this->data;
    }

    public function setBlogPostData(BlogPostData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getBlogPost(): BlogPost
    {
        return $this->entity;
    }

    public function setBlogPost(BlogPost $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}