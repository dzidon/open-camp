<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationCampSearchData;
use App\Library\Data\Admin\CampSearchData as AdminCampSearchData;
use App\Library\Data\User\CampSearchData as UserCampSearchData;
use App\Library\Enum\Search\Data\User\CampSortEnum;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampImage;
use App\Model\Entity\User;
use App\Model\Library\Camp\AdminApplicationCampsResult;
use App\Model\Library\Camp\CampLifespan;
use App\Model\Library\Camp\CampLifespanCollection;
use App\Model\Library\Camp\UserCampCatalogResult;
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
    private TreeSearchInterface $treeSearch;

    public function __construct(ManagerRegistry $registry, TreeSearchInterface $treeSearch)
    {
        parent::__construct($registry, Camp::class);

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
        $isOpenOnly = $data->isOpenOnly();

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

        if ($isOpenOnly === true)
        {
            $queryBuilder
                ->leftJoin(Application::class, 'application', 'WITH', '
                    campDate.id = application.campDate AND 
                    application.isDraft = FALSE AND
                    (application.isAccepted IS NULL OR application.isAccepted = TRUE)
                ')
                ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                    application.id = applicationCamper.application
                ')
                ->andWhere('campDate.isClosed = FALSE')
                ->andWhere('campDate.startAt > :now')
                ->setParameter('now', new DateTimeImmutable('now'))
                ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
                ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
            ;
        }
        else if ($isOpenOnly === false)
        {
            $openCampIdsResult = $this->createQueryBuilder('openCamp')
                ->select('DISTINCT openCamp.id')
                ->leftJoin(CampDate::class, 'openCampDate', 'WITH', 'openCamp.id = openCampDate.camp')
                ->leftJoin(Application::class, 'otherApplication', 'WITH', '
                    openCampDate.id = otherApplication.campDate AND 
                    otherApplication.isDraft = FALSE AND
                    (otherApplication.isAccepted IS NULL OR otherApplication.isAccepted = TRUE)
                ')
                ->leftJoin(ApplicationCamper::class, 'otherApplicationCamper', 'WITH', '
                    otherApplication.id = otherApplicationCamper.application
                ')
                ->andWhere('openCampDate.isClosed = FALSE')
                ->andWhere('openCampDate.startAt > :nowInSubQuery')
                ->setParameter('nowInSubQuery', new DateTimeImmutable('now'))
                ->andHaving('(openCampDate.isOpenAboveCapacity = TRUE OR COUNT(otherApplicationCamper.id) < openCampDate.capacity)')
                ->addGroupBy('openCampDate.id, openCampDate.isOpenAboveCapacity, openCampDate.capacity')
                ->getQuery()
                ->getArrayResult()
            ;

            $openCampIds = array_column($openCampIdsResult, 'id');
            $openCampIdsBinary = array_map(function (UuidV4 $id) {
                return $id->toBinary();
            }, $openCampIds);

            if (!empty($openCampIdsBinary))
            {
                $queryBuilder
                    ->andWhere('camp.id NOT IN (:openCampIds)')
                    ->setParameter('openCampIds', $openCampIdsBinary)
                ;
            }
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
        $isFeaturedOnly = $data->isFeaturedOnly();
        $sortBy = $data->getSortBy();
        $phrase = $data->getPhrase();
        $age = $data->getAge();
        $from = $data->getFrom();
        $to = $data->getTo();
        $isOpenOnly = $data->isOpenOnly();

        /*
         * Fetch camps based on search
         */
        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('camp, MIN(campDate.deposit + campDate.priceWithoutDeposit) AS HIDDEN lowestFullPrice, MIN(campDate.startAt) AS HIDDEN lowestStartAt')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', '
                camp.id = campDate.camp AND
                campDate.startAt > :now AND 
                campDate.isHidden IN (:hiddenValues)'
            )
            ->setParameter('now', new DateTimeImmutable('now'))
            ->setParameter('hiddenValues', $showHidden ? [true, false] : [false])
            ->andWhere('camp.name LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->addGroupBy('camp.id')
        ;

        // sorting
        if ($sortBy === CampSortEnum::LOWEST_FULL_PRICE_ASC)
        {
            $queryBuilder->addOrderBy('CASE WHEN MIN(campDate.deposit + campDate.priceWithoutDeposit) IS NULL THEN 1 ELSE 0 END', 'ASC');
        }

        if ($sortBy === CampSortEnum::LOWEST_START_AT_ASC)
        {
            $queryBuilder->addOrderBy('CASE WHEN MIN(campDate.startAt) IS NULL THEN 1 ELSE 0 END', 'ASC');
        }

        $queryBuilder->addOrderBy($sortBy->property(), $sortBy->order());

        // featured only
        if ($isFeaturedOnly)
        {
            $queryBuilder->andWhere('camp.isFeatured = TRUE');
        }

        // filter
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
            $queryBuilder
                ->leftJoin(Application::class, 'application', 'WITH', '
                    campDate.id = application.campDate AND 
                    application.isDraft = FALSE AND
                    (application.isAccepted IS NULL OR application.isAccepted = TRUE)
                ')
                ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                    application.id = applicationCamper.application
                ')
                ->andWhere('campDate.isClosed = FALSE')
                ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
                ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
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

        // all upcoming camp dates for displayed camps, open or not
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('campDate, camp')
            ->from(CampDate::class, 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('campDate.camp IN (:campIds)')
            ->setParameter('campIds', $campIds)
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->orderBy('campDate.startAt', 'ASC')
        ;

        if (!$showHidden)
        {
            $queryBuilder->andWhere('campDate.isHidden = FALSE');
        }

        $campDates = $queryBuilder
            ->getQuery()
            ->getResult()
        ;

        $campDateIds = array_map(function (CampDate $campDate) {
            return $campDate->getId()->toBinary();
        }, $campDates);

        // open camp dates
        $openCampDates = $this->_em->createQueryBuilder()
            ->select('campDate')
            ->from(CampDate::class, 'campDate')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                (application.isAccepted IS NULL OR application.isAccepted = TRUE)
            ')
            ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                application.id = applicationCamper.application
            ')
            ->andWhere('campDate.id IN (:campDateIds)')
            ->setParameter('campDateIds', $campDateIds)
            ->andWhere('campDate.isClosed = FALSE')
            ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
            ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
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

        return new UserCampCatalogResult($paginator, $campImages, $campDates, $openCampDates);
    }

    /**
     * @inheritDoc
     */
    public function getAdminApplicationCampsResult(ApplicationCampSearchData $data,
                                                   ?User                     $guide,
                                                   int                       $currentPage,
                                                   int                       $pageSize): AdminApplicationCampsResult
    {
        // paginator

        $sortBy = $data->getSortBy();
        $phrase = $data->getPhrase();

        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('camp, COUNT(application.id) AS HIDDEN numberOfPendingApplications')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', 'camp.id = campDate.camp')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                application.isAccepted IS NULL
            ')
            ->andWhere('camp.name LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->groupBy('camp.id')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($guide !== null)
        {
            $queryBuilder
                ->leftJoin('campDate.campDateUsers', 'campDateUser')
                ->andWhere('campDateUser.user = :guideId')
                ->setParameter('guideId', $guide->getId(), UuidType::NAME)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        // numbers of pending applications

        $camps = $paginator->getCurrentPageItems();
        $campBinaryIds = array_map(function (Camp $camp) {
            return $camp->getId()->toBinary();
        }, $camps);

        $queryBuilder = $this->createQueryBuilder('camp')
            ->select('camp.id, COUNT(application.id) AS numberOfPendingApplications')
            ->leftJoin(CampDate::class, 'campDate', 'WITH', 'camp.id = campDate.camp')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                application.isAccepted IS NULL
            ')
            ->andWhere('camp.id IN (:ids)')
            ->setParameter('ids', $campBinaryIds)
            ->groupBy('camp.id')
        ;

        if ($guide !== null)
        {
            $queryBuilder
                ->leftJoin('campDate.campDateUsers', 'campDateUser')
                ->andWhere('campDateUser.user = :guideId')
                ->setParameter('guideId', $guide->getId(), UuidType::NAME)
            ;
        }

        $queryResult = $queryBuilder
            ->getQuery()
            ->getArrayResult()
        ;

        $numbersOfPendingApplications = [];

        foreach ($queryResult as $data)
        {
            /** @var UuidV4 $campId */
            $campId = $data['id'];
            $campIdString = $campId->toRfc4122();
            $numbersOfPendingApplications[$campIdString] = $data['numberOfPendingApplications'];
        }

        return new AdminApplicationCampsResult($paginator, $numbersOfPendingApplications);
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