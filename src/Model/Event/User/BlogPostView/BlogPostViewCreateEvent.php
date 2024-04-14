<?php

namespace App\Model\Event\User\BlogPostView;

use App\Model\Entity\BlogPost;
use App\Model\Entity\BlogPostView;
use App\Model\Event\AbstractModelEvent;
use Symfony\Component\Uid\UuidV4;

class BlogPostViewCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.blog_post_view.create';

    private BlogPost $blogPost;

    private ?UuidV4 $visitorId;

    private ?BlogPostView $blogPostView = null;

    public function __construct(BlogPost $blogPost, ?UuidV4 $visitorId)
    {
        $this->blogPost = $blogPost;
        $this->visitorId = $visitorId;
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

    public function getVisitorId(): ?UuidV4
    {
        return $this->visitorId;
    }

    public function setVisitorId(?UuidV4 $visitorId): self
    {
        $this->visitorId = $visitorId;

        return $this;
    }

    public function getBlogPostView(): ?BlogPostView
    {
        return $this->blogPostView;
    }

    public function setBlogPostView(?BlogPostView $blogPostView): self
    {
        $this->blogPostView = $blogPostView;

        return $this;
    }
}