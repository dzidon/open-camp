<?php

namespace App\Model\Repository;

use App\Model\Entity\BlogPost;
use App\Model\Entity\BlogPostView;
use Symfony\Component\Uid\UuidV4;

interface BlogPostViewRepositoryInterface
{
    /**
     * Saves a blog post view.
     *
     * @param BlogPostView $blogPostView
     * @param bool $flush
     * @return void
     */
    public function saveBlogPostView(BlogPostView $blogPostView, bool $flush): void;

    /**
     * Removes a blog post view.
     *
     * @param BlogPostView $blogPostView
     * @param bool $flush
     * @return void
     */
    public function removeBlogPostView(BlogPostView $blogPostView, bool $flush): void;

    /**
     * Gets the view count of the given blog post.
     *
     * @param BlogPost $blogPost
     * @return int
     */
    public function getViewCountForBlogPost(BlogPost $blogPost): int;

    /**
     * Returns true if the given blog post has been seen by the given visitor.
     *
     * @param BlogPost $blogPost
     * @param UuidV4 $visitorId
     * @return bool
     */
    public function hasVisitorSeenBlogPost(BlogPost $blogPost, UuidV4 $visitorId): bool;
}