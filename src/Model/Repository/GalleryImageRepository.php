<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\GalleryImageSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
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
            ->orderBy('galleryImage.createdAt', 'DESC')
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
}