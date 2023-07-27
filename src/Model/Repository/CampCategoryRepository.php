<?php

namespace App\Model\Repository;

use App\Model\Entity\CampCategory;
use App\Search\DataStructure\GraphSearchInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CampCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampCategoryRepository extends AbstractRepository implements CampCategoryRepositoryInterface
{
    private GraphSearchInterface $graphSearch;

    public function __construct(ManagerRegistry $registry, GraphSearchInterface $graphSearch)
    {
        parent::__construct($registry, CampCategory::class);

        $this->graphSearch = $graphSearch;
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
    public function createCampCategory(string $name, string $urlName): CampCategory
    {
        return new CampCategory($name, $urlName);
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
        return $this->createQueryBuilder('campCategory')
            ->select('campCategory, campCategoryParent, campCategoryChild')
            ->leftJoin('campCategory.parent', 'campCategoryParent')
            ->leftJoin('campCategory.children', 'campCategoryChild')
            ->getQuery()
            ->getResult()
        ;
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
    public function findPossibleParents(CampCategory $category): array
    {
        $possibleParents = $this->findAll();

        $illegalParents = $this->graphSearch->getDescendentsOfNode($category);
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