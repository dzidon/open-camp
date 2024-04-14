<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\BlogPostSearchData as AdminBlogPostSearchData;
use App\Library\Data\User\BlogPostSearchData as UserBlogPostSearchData;
use App\Library\Enum\Search\Data\User\BlogPostSortEnum;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\BlogPost;
use App\Model\Entity\BlogPostView;
use App\Model\Library\BlogPost\BlogPostResult;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends AbstractRepository implements BlogPostRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @inheritDoc
     */
    public function saveBlogPost(BlogPost $blogPost, bool $flush): void
    {
        $this->save($blogPost, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeBlogPost(BlogPost $blogPost, bool $flush): void
    {
        $this->remove($blogPost, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?BlogPost
    {
        return $this->createQueryBuilder('blogPost')
            ->select('blogPost, author')
            ->leftJoin('blogPost.author', 'author')
            ->andWhere('blogPost.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByUrlName(string $urlName): ?BlogPost
    {
        return $this->createQueryBuilder('blogPost')
            ->select('blogPost, author')
            ->leftJoin('blogPost.author', 'author')
            ->andWhere('blogPost.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminSearchResult(AdminBlogPostSearchData $data, int $currentPage, int $pageSize): BlogPostResult
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $author = $data->getAuthor();
        $isHidden = $data->isHidden();
        $isPinned = $data->isPinned();

        // blog posts

        $queryBuilder = $this->createQueryBuilder('blogPost')
            ->select('blogPost, author, COUNT(blogPostView.id) AS HIDDEN blogPostViewCount')
            ->leftJoin('blogPost.author', 'author')
            ->leftJoin(BlogPostView::class, 'blogPostView', 'WITH', '
                blogPost.id = blogPostView.blogPost
            ')
            ->andWhere('blogPost.title LIKE :phrase OR blogPost.urlName LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
            ->groupBy('blogPost.id')
        ;

        if ($author !== null)
        {
            $queryBuilder
                ->andWhere('blogPost.author = :authorId')
                ->setParameter('authorId', $author->getId(), UuidType::NAME)
            ;
        }

        if ($isHidden !== null)
        {
            $queryBuilder
                ->andWhere('blogPost.isHidden = :isHidden')
                ->setParameter('isHidden', $isHidden)
            ;
        }

        if ($isPinned !== null)
        {
            $queryBuilder
                ->andWhere('blogPost.isPinned = :isPinned')
                ->setParameter('isPinned', $isPinned)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
        $blogPosts = $paginator->getCurrentPageItems();
        $blogPostBinaryIds = $this->getBlogPostIds($blogPosts);

        // view counts

        $viewCountsArrayResult = $this->createQueryBuilder('blogPost')
            ->select('blogPost.id AS blogPostId, COUNT(blogPostView.id) AS blogPostViewCount')
            ->leftJoin(BlogPostView::class, 'blogPostView', 'WITH', '
                blogPost.id = blogPostView.blogPost
            ')
            ->andWhere('blogPost.id IN (:ids)')
            ->setParameter('ids', $blogPostBinaryIds)
            ->groupBy('blogPost.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $viewCounts = [];

        foreach ($viewCountsArrayResult as $data)
        {
            /** @var UuidV4 $blogPostId */
            $blogPostId = $data['blogPostId'];
            $blogPostIdString = $blogPostId->toRfc4122();
            $blogPostViewCount = $data['blogPostViewCount'];

            $viewCounts[$blogPostIdString] = $blogPostViewCount;
        }

        return new BlogPostResult($paginator, $viewCounts);
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(UserBlogPostSearchData $data, bool $showHidden, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $queryBuilder = $this->createQueryBuilder('blogPost')
            ->andWhere('blogPost.title LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
        ;

        if ($sortBy === BlogPostSortEnum::CREATED_AT_DESC)
        {
            $queryBuilder->addOrderBy('blogPost.isPinned', 'DESC');
        }

        $queryBuilder->addOrderBy($sortBy->property(), $sortBy->order());

        if (!$showHidden)
        {
            $queryBuilder->andWhere('blogPost.isHidden = FALSE');
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    private function getBlogPostIds(array|BlogPost $blogPosts): array
    {
        if ($blogPosts instanceof BlogPost)
        {
            $blogPosts = [$blogPosts];
        }

        return array_map(function (BlogPost $blogPost) {
            return $blogPost->getId()->toBinary();
        }, $blogPosts);
    }
}