<?php

namespace App\Model\Event\Admin\BlogPost;

use App\Library\Data\Admin\BlogPostData;
use App\Model\Entity\BlogPost;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class BlogPostCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.blog_post.create';

    private BlogPostData $data;

    private User $author;

    private ?BlogPost $entity = null;

    public function __construct(BlogPostData $data, User $author)
    {
        $this->data = $data;
        $this->author = $author;
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

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->entity;
    }

    public function setBlogPost(?BlogPost $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}