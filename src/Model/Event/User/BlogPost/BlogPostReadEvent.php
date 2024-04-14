<?php

namespace App\Model\Event\User\BlogPost;

use App\Model\Entity\BlogPost;
use App\Model\Event\AbstractModelEvent;

class BlogPostReadEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.blog_post.read';

    private BlogPost $blogPost;

    public function __construct(BlogPost $blogPost)
    {
        $this->blogPost = $blogPost;
    }

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}