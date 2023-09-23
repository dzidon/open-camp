<?php

namespace App\Model\Repository;

use App\Model\Entity\CampCategory;
use App\Service\Search\DataStructure\TreeSearchInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method CampCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampCategoryRepository extends AbstractRepository implements CampCategoryRepositoryInterface
{
    private TreeSearchInterface $treeSearch;

    public function __construct(ManagerRegistry $registry, TreeSearchInterface $treeSearch)
    {
        parent::__construct($registry, CampCategory::class);

        $this->treeSearch = $treeSearch;
    }

    /**
     * @inheritDoc
     */
    public function saveCampCategory(CampCategory $campCategory, bool $flush): void
    {
        $this->save($campCategory, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampCategory(CampCategory $campCategory, bool $flush): void
    {
        $this->remove($campCategory, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?CampCategory
    {
        return $this->createQueryBuilder('campCategory')
            ->select('campCategory, campCategoryParent, campCategoryChild')
            ->leftJoin('campCategory.parent', 'campCategoryParent')
            ->leftJoin('campCategory.children', 'campCategoryChild')
            ->andWhere('campCategory.id = :id')
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
        $campCategories = $this->createQueryBuilder('campCategory')
            ->select('campCategory, campCategoryParent, campCategoryChild')
            ->leftJoin('campCategory.parent', 'campCategoryParent')
            ->leftJoin('campCategory.children', 'campCategoryChild')
            ->getQuery()
            ->getResult()
        ;

        $campCategoryPaths = [];

        foreach ($campCategories as $campCategory)
        {
            $path = $campCategory->getPath();
            $campCategoryId = $campCategory->getId();
            $campCategoryPaths[$campCategoryId->toRfc4122()] = $path;
        }

        usort($campCategories, function (CampCategory $campCategoryA, CampCategory $campCategoryB) use ($campCategoryPaths)
        {
            $idA = $campCategoryA->getId();
            $idB = $campCategoryB->getId();

            return $campCategoryPaths[$idA->toRfc4122()] <=> $campCategoryPaths[$idB->toRfc4122()];
        });

        return $campCategories;
    }

    /**
     * @inheritDoc
     */
    public function findRoots(): array
    {
        return array_filter($this->findAll(), function (CampCategory $campCategory) {
            return $campCategory->getParent() === null;
        });
    }

    /**
     * @inheritDoc
     */
    public function findOneByPath(string $path): ?CampCategory
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

            /** @var CampCategory $campCategory */
            $campCategory = $this->treeSearch->getDescendentByPath($root, $relativePath, 'urlName');

            return $campCategory;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findPossibleParents(CampCategory $category): array
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
        return $this->createQueryBuilder('campCategory')
            ->select('campCategory, campCategoryParent, campCategoryChild')
            ->leftJoin('campCategory.parent', 'campCategoryParent')
            ->leftJoin('campCategory.children', 'campCategoryChild')
            ->andWhere('campCategory.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getResult()
        ;
    }
}