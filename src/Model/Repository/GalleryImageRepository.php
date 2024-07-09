<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\GalleryImageSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Library\GalleryImage\GalleryImageSurroundingsResult;
use App\Service\Search\DataStructure\TreeSearchInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method GalleryImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryImage[]    findAll()
 * @method GalleryImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryImageRepository extends AbstractRepository implements GalleryImageRepositoryInterface
{
    private TreeSearchInterface $treeSearch;

    public function __construct(ManagerRegistry $registry, TreeSearchInterface $treeSearch)
    {
        parent::__construct($registry, GalleryImage::class);

        $this->treeSearch = $treeSearch;
    }

    /**
     * @inheritDoc
     */
    public function saveGalleryImage(GalleryImage $galleryImage, bool $flush): void
    {
        $this->save($galleryImage, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeGalleryImage(GalleryImage $galleryImage, bool $flush): void
    {
        $this->remove($galleryImage, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?GalleryImage
    {
        return $this->createQueryBuilder('galleryImage')
            ->select('galleryImage, galleryImageCategory')
            ->leftJoin('galleryImage.galleryImageCategory', 'galleryImageCategory')
            ->andWhere('galleryImage.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findForCarousel(): array
    {
        return $this->createQueryBuilder('galleryImage')
            ->andWhere('galleryImage.isInCarousel = TRUE')
            ->orderBy('galleryImage.carouselPriority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByGalleryImageCategory(GalleryImageCategory $galleryImageCategory,
                                               bool                 $offspringCategories = false): array
    {
        $queryBuilder = $this->createQueryBuilder('galleryImage')
            ->select('galleryImage, galleryImageCategory')
            ->leftJoin('galleryImage.galleryImageCategory', 'galleryImageCategory')
        ;

        if ($offspringCategories)
        {
            $galleryImageCategories = $this->treeSearch->getDescendentsOfNode($galleryImageCategory);
            $galleryImageCategories[] = $galleryImageCategory;

            $galleryImageCategoryIds = array_map(function (GalleryImageCategory $galleryImageCategory) {
                return $galleryImageCategory->getId()->toBinary();
            }, $galleryImageCategories);

            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory IN (:galleryImageCategoryIds)')
                ->setParameter('galleryImageCategoryIds', $galleryImageCategoryIds)
            ;
        }
        else
        {
            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory = :galleryImageCategoryId')
                ->setParameter('galleryImageCategoryId', $galleryImageCategory->getId(), UuidType::NAME)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function existsAtLeastOneImageInGallery(): bool
    {
        $count = $this->createQueryBuilder('galleryImage')
            ->select('COUNT(galleryImage.id)')
            ->andWhere('galleryImage.isHiddenInGallery = FALSE')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(GalleryImageSearchData $galleryImageSearchData,
                                      int                    $currentPage,
                                      int                    $pageSize): DqlPaginator
    {
        $galleryImageCategory = $galleryImageSearchData->getGalleryImageCategory();
        $sortBy = $galleryImageSearchData->getSortBy();
        $isHiddenInGallery = $galleryImageSearchData->getIsHiddenInGallery();
        $isInCarousel = $galleryImageSearchData->getIsInCarousel();

        $queryBuilder = $this->createQueryBuilder('galleryImage')
            ->select('galleryImage, galleryImageCategory')
            ->leftJoin('galleryImage.galleryImageCategory', 'galleryImageCategory')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($galleryImageCategory === false)
        {
            $queryBuilder->andWhere('galleryImage.galleryImageCategory IS NULL');
        }
        else if ($galleryImageCategory !== null)
        {
            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory = :galleryImageCategoryId')
                ->setParameter('galleryImageCategoryId', $galleryImageCategory->getId(), UuidType::NAME)
            ;
        }

        if ($isHiddenInGallery !== null)
        {
            $queryBuilder
                ->andWhere('galleryImage.isHiddenInGallery = :isHiddenInGallery')
                ->setParameter('isHiddenInGallery', $isHiddenInGallery)
            ;
        }

        if ($isInCarousel !== null)
        {
            $queryBuilder
                ->andWhere('galleryImage.isInCarousel = :isInCarousel')
                ->setParameter('isInCarousel', $isInCarousel)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(?GalleryImageCategory $galleryImageCategory,
                                     int                   $currentPage,
                                     int                   $pageSize): DqlPaginator
    {
        $queryBuilder = $this->createQueryBuilder('galleryImage')
            ->andWhere('galleryImage.isHiddenInGallery = FALSE')
            ->orderBy('galleryImage.priority', 'DESC')
        ;

        if ($galleryImageCategory !== null)
        {
            $galleryImageCategories = $this->treeSearch->getDescendentsOfNode($galleryImageCategory);
            $galleryImageCategories[] = $galleryImageCategory;

            $galleryImageCategoryIds = array_map(function (GalleryImageCategory $galleryImageCategory) {
                return $galleryImageCategory->getId()->toBinary();
            }, $galleryImageCategories);

            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory IN (:galleryImageCategoryIds)')
                ->setParameter('galleryImageCategoryIds', $galleryImageCategoryIds)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getHighestPriority(): ?int
    {
        return $this->createQueryBuilder('galleryImage')
            ->select('max(galleryImage.priority)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getGalleryImageSurroundings(GalleryImage          $galleryImage,
                                                ?GalleryImageCategory $fromGalleryImageCategory = null): ?GalleryImageSurroundingsResult
    {
        $galleryImageCategory = $galleryImage->getGalleryImageCategory();
        $galleryImageCategories = null;
        $galleryImageCategoryIds = null;

        if ($galleryImageCategory !== null && $fromGalleryImageCategory !== null)
        {
            $galleryImageCategories = $this->treeSearch->getDescendentsOfNode($fromGalleryImageCategory);
            $galleryImageCategories[] = $fromGalleryImageCategory;

            if (!in_array($galleryImageCategory, $galleryImageCategories))
            {
                return null;
            }
        }

        if ($galleryImageCategories !== null)
        {
            $galleryImageCategoryIds = array_map(function (GalleryImageCategory $galleryImageCategory) {
                return $galleryImageCategory->getId()->toBinary();
            }, $galleryImageCategories);
        }

        $priority = $galleryImage->getPriority();

        // previous
        $queryBuilder = $this->createQueryBuilder('galleryImage')
            ->andWhere('galleryImage.isHiddenInGallery = FALSE')
            ->andWhere('galleryImage.priority > :priority')
            ->setParameter('priority', $priority)
            ->orderBy('galleryImage.priority', 'ASC')
            ->setMaxResults(1)
        ;

        if ($galleryImageCategoryIds !== null)
        {
            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory IN (:galleryImageCategoryIds)')
                ->setParameter('galleryImageCategoryIds', $galleryImageCategoryIds)
            ;
        }

        /** @var GalleryImage|null $galleryImagePrevious */
        $galleryImagePrevious = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        // next
        $queryBuilder = $this->createQueryBuilder('galleryImage')
            ->andWhere('galleryImage.isHiddenInGallery = FALSE')
            ->andWhere('galleryImage.priority < :priority')
            ->setParameter('priority', $priority)
            ->orderBy('galleryImage.priority', 'DESC')
            ->setMaxResults(1)
        ;

        if ($galleryImageCategoryIds !== null)
        {
            $queryBuilder
                ->andWhere('galleryImage.galleryImageCategory IN (:galleryImageCategoryIds)')
                ->setParameter('galleryImageCategoryIds', $galleryImageCategoryIds)
            ;
        }

        /** @var GalleryImage|null $galleryImageNext */
        $galleryImageNext = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return new GalleryImageSurroundingsResult($galleryImage, $galleryImagePrevious, $galleryImageNext);
    }
}