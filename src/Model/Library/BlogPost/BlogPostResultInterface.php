<?php

namespace App\Model\Library\BlogPost;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\BlogPost;

/**
 * Holds blog posts and their view counts.
 */
interface BlogPostResultInterface
{
    /**
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface;

    /**
     * @param string|BlogPost $blogPost
     * @return int|null If null is returned, the view count is not known for the given blog post.
     */
    public function getViewCount(string|BlogPost $blogPost): ?int;
}