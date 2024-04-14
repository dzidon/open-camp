<?php

namespace App\Model\Event\Admin\BlogPost;

use App\Model\Entity\BlogPost;
use App\Model\Event\AbstractModelEvent;

class BlogPostDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.blog_post.delete';

    private BlogPost $entity;

    public function __construct(BlogPost $entity)
    {
        $this->entity = $entity;
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