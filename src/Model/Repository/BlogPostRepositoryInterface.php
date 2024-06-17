<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\BlogPostSearchData as AdminBlogPostSearchData;
use App\Library\Data\User\BlogPostSearchData as UserBlogPostSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\BlogPost;
use App\Model\Library\BlogPost\BlogPostResultInterface;
use Symfony\Component\Uid\UuidV4;

interface BlogPostRepositoryInterface
{
    /**
     * Saves a blog post.
     *
     * @param BlogPost $blogPost
     * @param bool $flush
     * @return void
     */
    public function saveBlogPost(BlogPost $blogPost, bool $flush): void;

    /**
     * Removes a blog post.
     *
     * @param BlogPost $blogPost
     * @param bool $flush
     * @return void
     */
    public function removeBlogPost(BlogPost $blogPost, bool $flush): void;

    /**
     * Finds one blog post by id.
     *
     * @param UuidV4 $id
     * @return BlogPost|null
     */
    public function findOneById(UuidV4 $id): ?BlogPost;

    /**
     * Finds one blog post by url name.
     *
     * @param string $urlName
     * @return BlogPost|null
     */
    public function findOneByUrlName(string $urlName): ?BlogPost;

    /**
     * Returns true if there is at least one blog post.
     *
     * @param bool $includeHidden
     * @return bool
     */
    public function existsAtLeastOneBlogPost(bool $includeHidden): bool;

    /**
     * Returns admin blog post search result.
     *
     * @param AdminBlogPostSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return BlogPostResultInterface
     */
    public function getAdminSearchResult(AdminBlogPostSearchData $data, int $currentPage, int $pageSize): BlogPostResultInterface;

    /**
     * Returns user blog post paginator.
     *
     * @param UserBlogPostSearchData $data
     * @param bool $showHidden
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(UserBlogPostSearchData $data, bool $showHidden, int $currentPage, int $pageSize): PaginatorInterface;
}