<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchData as AdminCampSearchData;
use App\Library\Data\User\CampSearchData as UserCampSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampImage;
use App\Model\Module\CampCatalog\Camp\CampLifespan;
use App\Model\Module\CampCatalog\Camp\CampLifespanCollection;
use App\Model\Module\CampCatalog\Camp\UserCampCatalogResult;
use App\Model\Module\CampCatalog\CampImage\CampImageFilesystemInterface;
use App\Service\Search\DataStructure\TreeSearchInterface;
use DateTimeImmutable;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Camp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Camp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Camp[]    findAll()
 * @method Camp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampRepository extends AbstractRepository implements CampRepositoryInterface
{
    private CampImageRepositoryInterface $campImageRepository;
    private CampImageFilesystemInterface $campImageFilesystem;
    private TreeSearchInterface $treeSearch;

    public function __construct(ManagerRegistry              $registry,
                                CampImageRepositoryInterface $campImageRepository,
                                CampImageFilesystemInterface $campImageFilesystem,
                                TreeSearchInterface          $treeSearch)
    {
        parent::__construct($registry, Camp::class);

        $this->campImageRepository = $campImageRepository;
        $this->campImageFilesystem = $campImageFilesystem;
        $this->treeSearch = $treeSearch;
    }

    /**
     * @inheritDoc
     */
    public function saveCamp(Camp $camp, bool $flush): void
    {
        $this->save($camp, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCamp(Camp $camp, bool $flush): void
    {
        $campImages = $this->campImageRepository->findByCamp($camp);

        foreach ($campImages as $campImage)
        {
            $this->campImageFilesystem->removeFile($campImage);
        }

        $this->remove($camp, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Camp
    {
        return $this->createQueryBuilder('camp')
            ->select('camp, campCategory')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->andWhere('camp.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByUrlName(string $urlName): ?Camp
    {
        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('camp, campCategory')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->andWhere('camp.urlName = :urlName')
            ->setParameter('urlName', $urlName)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(AdminCampSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $age = $data->getAge();
        $from = $data->getFrom();
        $to = $data->getTo();
        $campCategory = $data->getCampCategory();
        $isFeatured = $data->isFeatured();
        $isHidden = $data->isHidden();
        $isActive = $data->isActive();

        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('DISTINCT camp')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', 'camp.id = campDate.camp')
            ->andWhere('(camp.name LIKE :phrase OR camp.urlName LIKE :phrase)')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($age !== null)
        {
            $queryBuilder
                ->andWhere(':age >= camp.ageMin')
                ->andWhere(':age <= camp.ageMax')
                ->setParameter('age', $age)
            ;
        }

        if ($from !== null)
        {
            $queryBuilder
                ->andWhere('campDate.startAt >= :from')
                ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ;
        }

        if ($to !== null)
        {
            $queryBuilder
                ->andWhere('campDate.endAt <= :to')
                ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ;
        }

        if ($campCategory === false)
        {
            $queryBuilder->andWhere('camp.campCategory IS NULL');
        }
        else if ($campCategory !== null)
        {
            $queryBuilder
                ->andWhere('camp.campCategory = :campCategoryId')
                ->setParameter('campCategoryId', $campCategory->getId(), UuidType::NAME)
            ;
        }

        if ($isFeatured !== null)
        {
            $queryBuilder
                ->andWhere('camp.isFeatured = :isFeatured')
                ->setParameter('isFeatured', $isFeatured)
            ;
        }

        if ($isHidden !== null)
        {
            $queryBuilder
                ->andWhere('camp.isHidden = :isHidden')
                ->setParameter('isHidden', $isHidden)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getUserCampCatalogResult(UserCampSearchData $data,
                                             ?CampCategory      $campCategory,
                                             bool               $showHidden,
                                             int                $currentPage,
                                             int                $pageSize): UserCampCatalogResult
    {
        $phrase = $data->getPhrase();
        $age = $data->getAge();
        $from = $data->getFrom();
        $to = $data->getTo();
        $isOpenOnly = $data->isOpenOnly();

        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('DISTINCT camp')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', 'camp.id = campDate.camp')
            ->andWhere('camp.name LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy('camp.priority', 'DESC')
        ;

        if (!$showHidden)
        {
            $queryBuilder->andWhere('camp.isHidden = FALSE');
        }

        if ($age !== null)
        {
            $queryBuilder
                ->andWhere(':age >= camp.ageMin')
                ->andWhere(':age <= camp.ageMax')
                ->setParameter('age', $age)
            ;
        }

        if ($from !== null)
        {
            $from = $from->setTime(0, 0);

            $queryBuilder
                ->andWhere('campDate.startAt >= :from')
                ->setParameter('from', $from)
            ;
        }

        if ($to !== null)
        {
            $to = $to->setTime(23, 59, 59);

            $queryBuilder
                ->andWhere('campDate.endAt <= :to')
                ->setParameter('to', $to)
            ;
        }

        if ($isOpenOnly)
        {
            // todo: only search camps with open dates

            $queryBuilder
                ->andWhere('campDate.startAt > :now')
                ->setParameter('now', new DateTimeImmutable('now'))
            ;
        }

        if ($campCategory !== null)
        {
            $campCategories = $this->treeSearch->getDescendentsOfNode($campCategory);
            $campCategories[] = $campCategory;

            $campCategoryIds = array_map(function (CampCategory $campCategory) {
                return $campCategory->getId()->toBinary();
            }, $campCategories);

            $queryBuilder
                ->andWhere('camp.campCategory IN (:campCategoryIds)')
                ->setParameter('campCategoryIds', $campCategoryIds)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        /*
         * Fetch dates for camps
         */
        $camps = $paginator->getCurrentPageItems();

        $campIds = array_map(function (Camp $camp) {
            return $camp->getId()->toBinary();
        }, $camps);

        $campDates = $this->_em->createQueryBuilder()
            ->select('campDate, camp')
            ->from(CampDate::class, 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('campDate.camp IN (:campIds)')
            ->setParameter('campIds', $campIds)
            // todo: only search open dates
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->orderBy('campDate.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        /*
         * Fetch images for camps
         */
        $campImages = $this->_em->createQuery('
                SELECT campImage, camp
                FROM ' . CampImage::class . ' campImage
                LEFT JOIN campImage.camp camp
                LEFT JOIN ' . CampImage::class . ' campImageJoined
                    WITH campImage.camp = campImageJoined.camp
                    AND campImage.priority < campImageJoined.priority
                WHERE campImageJoined.priority IS NULL
                    AND campImage.camp IN (:campIds)
                GROUP BY campImage.camp
                ORDER BY campImage.priority DESC
            ')
            ->setParameter('campIds', $campIds)
            ->getResult()
        ;

        return new UserCampCatalogResult($paginator, $campImages, $campDates);
    }

    /**
     * @inheritDoc
     */
    public function getCampLifespanCollection(array $camps): CampLifespanCollection
    {
        foreach ($camps as $camp)
        {
            if (!$camp instanceof Camp)
            {
                throw new LogicException(
                    sprintf('Array "camps" in "%s" contains an instance of "%s", but can only contain instances of "%s".', __METHOD__, $camp::class, Camp::class)
                );
            }
        }

        $campIds = array_map(function (Camp $camp) {
            return $camp->getId()->toBinary();
        }, $camps);

        $results = $this->createQueryBuilder('camp')
            ->select('camp.id, min(campDate.startAt) as min, max(campDate.endAt) as max')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', 'camp.id = campDate.camp')
            ->andWhere('camp.id IN (:ids)')
            ->setParameter('ids', $campIds)
            ->groupBy('camp.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $collection = new CampLifespanCollection();

        foreach ($results as $result)
        {
            $startAt = $result['min'] === null ? null : new DateTimeImmutable($result['min']);
            $endAt = $result['max'] === null ? null : new DateTimeImmutable($result['max']);
            $campLifespan = new CampLifespan($startAt, $endAt);
            $collection->addCampLifespan($result['id'], $campLifespan);
        }

        return $collection;
    }
}