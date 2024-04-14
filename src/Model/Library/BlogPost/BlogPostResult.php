<?php

namespace App\Model\Library\BlogPost;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\BlogPost;
use LogicException;

/**
 * @inheritDoc
 */
class BlogPostResult implements BlogPostResultInterface
{
    private PaginatorInterface $paginator;

    private array $viewCounts = [];
    
    public function __construct(PaginatorInterface $paginator, array $viewCounts = [])
    {
        $blogPostIdStrings = [];

        // paginator

        foreach ($paginator->getCurrentPageItems() as $blogPost)
        {
            if (!$blogPost instanceof BlogPost)
            {
                throw new LogicException(
                    sprintf('Paginator passed to "%s" can only contain instances of "%s".', $this::class, BlogPost::class)
                );
            }

            $blogPostIdString = $blogPost
                ->getId()
                ->toRfc4122()
            ;

            $blogPostIdStrings[$blogPostIdString] = $blogPostIdString;
        }

        $this->paginator = $paginator;
        
        // view counts

        foreach ($viewCounts as $blogPostIdString => $viewCount)
        {
            if (!array_key_exists($blogPostIdString, $blogPostIdStrings))
            {
                throw new LogicException(
                    sprintf('View count for blog post ID "%s" passed to "%s" is not valid, there is no blog post with the given ID in the paginator result.', $blogPostIdString, $this::class)
                );
            }

            if (!is_int($viewCount))
            {
                throw new LogicException(
                    sprintf('View count for blog post ID "%s" passed to "%s" is not valid, the value must be an integer.', $blogPostIdString, $this::class)
                );
            }

            $this->viewCounts[$blogPostIdString] = $viewCount;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function getViewCount(string|BlogPost $blogPost): ?int
    {
        $blogPostIdString = $blogPost;

        if (!is_string($blogPostIdString))
        {
            $blogPostIdString = $blogPost
                ->getId()
                ->toRfc4122()
            ;
        }

        if (!array_key_exists($blogPostIdString, $this->viewCounts))
        {
            return null;
        }

        return $this->viewCounts[$blogPostIdString];
    }
}