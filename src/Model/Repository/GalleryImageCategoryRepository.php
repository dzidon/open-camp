<?php

namespace App\Model\Repository;

use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
use App\Service\Search\DataStructure\TreeSearchInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method GalleryImageCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryImageCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryImageCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryImageCategoryRepository extends AbstractRepository implements GalleryImageCategoryRepositoryInterface
{
    private TreeSearchInterface $treeSearch;

    public function __construct(ManagerRegistry $registry, TreeSearchInterface $treeSearch)
    {
        parent::__construct($registry, GalleryImageCategory::class);

        $this->treeSearch = $treeSearch;
    }

    /**
     * @inheritDoc
     */
    public function saveGalleryImageCategory(GalleryImageCategory $galleryImageCategory, bool $flush): void
    {
        $this->save($galleryImageCategory, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeGalleryImageCategory(GalleryImageCategory $galleryImageCategory, bool $flush): void
    {
        $this->remove($galleryImageCategory, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?GalleryImageCategory
    {
        return $this->createQueryBuilder('galleryImageCategory')
            ->select('galleryImageCategory, galleryImageCategoryParent, galleryImageCategoryChild')
            ->leftJoin('galleryImageCategory.parent', 'galleryImageCategoryParent')
            ->leftJoin('galleryImageCategory.children', 'galleryImageCategoryChild')
            ->andWhere('galleryImageCategory.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $galleryImageCategories = $this->createQueryBuilder('galleryImageCategory')
            ->select('galleryImageCategory, galleryImageCategoryParent, galleryImageCategoryChild')
            ->leftJoin('galleryImageCategory.parent', 'galleryImageCategoryParent')
            ->leftJoin('galleryImageCategory.children', 'galleryImageCategoryChild')
            ->getQuery()
            ->getResult()
        ;

        $galleryImageCategoryPaths = [];

        foreach ($galleryImageCategories as $galleryImageCategory)
        {
            $path = $galleryImageCategory->getPath();
            $galleryImageCategoryId = $galleryImageCategory->getId();
            $galleryImageCategoryPaths[$galleryImageCategoryId->toRfc4122()] = $path;
        }

        usort($galleryImageCategories, function (
            GalleryImageCategory $galleryImageCategoryA,
            GalleryImageCategory $galleryImageCategoryB
        ) use ($galleryImageCategoryPaths)
        {
            $idA = $galleryImageCategoryA->getId();
            $idB = $galleryImageCategoryB->getId();

            return $galleryImageCategoryPaths[$idA->toRfc4122()] <=> $galleryImageCategoryPaths[$idB->toRfc4122()];
        });

        return $galleryImageCategories;
    }

    /**
     * @inheritDoc
     */
    public function findRoots(): array
    {
        return array_filter($this->findAll(), function (GalleryImageCategory $galleryImageCategory) {
            return $galleryImageCategory->getParent() === null;
        });
    }

    /**
     * @inheritDoc
     */
    public function findOneByPath(string $path): ?GalleryImageCategory
    {
        $path = trim($path, '/');

        if ($path === '')
        {
            return null;
        }

        $urlNames = explode('/', $path);
        $urlNameKeyFirst = array_key_first($urlNames);
        $urlNameFirst = $urlNames[$urlNameKeyFirst];
        unset($urlNames[$urlNameKeyFirst]);
        $relativePath = implode('/', $urlNames);

        $roots = $this->findRoots();

        foreach ($roots as $root)
        {
            if ($root->getUrlName() !== $urlNameFirst)
            {
                continue;
            }

            /** @var GalleryImageCategory $galleryImageCategory */
            $galleryImageCategory = $this->treeSearch->getDescendentByPath($root, $relativePath, 'urlName');

            return $galleryImageCategory;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findPossibleParents(GalleryImageCategory $category): array
    {
        $possibleParents = $this->findAll();

        $illegalParents = $this->treeSearch->getDescendentsOfNode($category);
        $illegalParents[] = $category;

        foreach ($possibleParents as $key => $possibleParent)
        {
            if (in_array($possibleParent, $illegalParents))
            {
                unset($possibleParents[$key]);
            }
        }

        return $possibleParents;
    }

    /**
     * @inheritDoc
     */
    public function findByUrlName(string $urlName): array
    {
        return $this->createQueryBuilder('galleryImageCategory')
            ->select('galleryImageCategory, galleryImageCategoryParent, galleryImageCategoryChild')
            ->leftJoin('galleryImageCategory.parent', 'galleryImageCategoryParent')
            ->leftJoin('galleryImageCategory.children', 'galleryImageCategoryChild')
            ->andWhere('galleryImageCategory.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function galleryImageCategoryHasGalleryImage(GalleryImageCategory $galleryImageCategory): bool
    {
        $subTreeCampCategories = $this->treeSearch->getDescendentsOfNode($galleryImageCategory);
        $subTreeCampCategories[] = $galleryImageCategory;

        $subTreeGalleryImageCategoryIds = array_map(function (GalleryImageCategory $galleryImageCategory) {
            return $galleryImageCategory->getId()->toBinary();
        }, $subTreeCampCategories);

        $count = $this->_em->createQueryBuilder()
            ->select('count(galleryImage.id)')
            ->from(GalleryImage::class, 'galleryImage')
            ->andWhere('galleryImage.isHiddenInGallery = FALSE')
            ->andWhere('galleryImage.galleryImageCategory IN (:ids)')
            ->setParameter('ids', $subTreeGalleryImageCategoryIds)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function filterOutGalleryImageCategoriesWithoutGalleryImages(array $galleryImageCategories): array
    {
        $filteredGalleryImageCategories = [];

        foreach ($galleryImageCategories as $galleryImageCategory)
        {
            if ($this->galleryImageCategoryHasGalleryImage($galleryImageCategory))
            {
                $filteredGalleryImageCategories[] = $galleryImageCategory;
            }
        }

        return $filteredGalleryImageCategories;
    }
}