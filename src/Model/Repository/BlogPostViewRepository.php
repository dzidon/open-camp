<?php

namespace App\Model\Repository;

use App\Model\Entity\BlogPost;
use App\Model\Entity\BlogPostView;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method BlogPostView|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPostView|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPostView[]    findAll()
 * @method BlogPostView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostViewRepository extends AbstractRepository implements BlogPostViewRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostView::class);
    }

    /**
     * @inheritDoc
     */
    public function saveBlogPostView(BlogPostView $blogPostView, bool $flush): void
    {
        $this->save($blogPostView, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeBlogPostView(BlogPostView $blogPostView, bool $flush): void
    {
        $this->remove($blogPostView, $flush);
    }

    /**
     * @inheritDoc
     */
    public function getViewCountForBlogPost(BlogPost $blogPost): int
    {
        return $this->createQueryBuilder('blogPostView')
            ->select('COUNT(blogPostView.id)')
            ->andWhere('blogPostView.blogPost = :blogPostId')
            ->setParameter('blogPostId', $blogPost->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function hasVisitorSeenBlogPost(BlogPost $blogPost, UuidV4 $visitorId): bool
    {
        $count = $this->createQueryBuilder('blogPostView')
            ->select('COUNT(blogPostView.id)')
            ->andWhere('blogPostView.blogPost = :blogPostId')
            ->setParameter('blogPostId', $blogPost->getId(), UuidType::NAME)
            ->andWhere('blogPostView.visitorId = :visitorId')
            ->setParameter('visitorId', $visitorId, UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}