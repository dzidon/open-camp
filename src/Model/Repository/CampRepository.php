<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Module\CampCatalog\Camp\CampLifespan;
use App\Model\Module\CampCatalog\Camp\CampLifespanCollection;
use App\Model\Module\CampCatalog\CampImage\CampImageFilesystemInterface;
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

    public function __construct(ManagerRegistry              $registry,
                                CampImageRepositoryInterface $campImageRepository,
                                CampImageFilesystemInterface $campImageFilesystem)
    {
        parent::__construct($registry, Camp::class);

        $this->campImageRepository = $campImageRepository;
        $this->campImageFilesystem = $campImageFilesystem;
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
    public function createCamp(string $name,
                               string $urlName,
                               int    $ageMin,
                               int    $ageMax,
                               string $street,
                               string $town,
                               string $zip,
                               string $country): Camp
    {
        return new Camp($name, $urlName, $ageMin, $ageMax, $street, $town, $zip, $country);
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
        return $this->createQueryBuilder('camp')
            ->select('camp, campCategory')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->andWhere('camp.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(CampSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $age = $data->getAge();
        $startAt = $data->getStartAt();
        $endAt = $data->getEndAt();
        $campCategory = $data->getCampCategory();
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

        if ($startAt !== null)
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->between('campDate.startAt', ':dayStart', ':dayEnd'))
                ->setParameter('dayStart', $startAt->format('Y-m-d 00:00:00'))
                ->setParameter('dayEnd', $startAt->format('Y-m-d 23:59:59'))
            ;
        }

        if ($endAt !== null)
        {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->between('campDate.endAt', ':dayStart', ':dayEnd'))
                ->setParameter('dayStart', $endAt->format('Y-m-d 00:00:00'))
                ->setParameter('dayEnd', $endAt->format('Y-m-d 23:59:59'))
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

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, true), $currentPage, $pageSize);
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