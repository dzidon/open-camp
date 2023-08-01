<?php

namespace App\Model\Repository;

use App\Enum\Entity\ContactRoleEnum;
use App\Form\DataTransfer\Data\User\ContactSearchDataInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Search\Paginator\DqlPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends AbstractRepository implements ContactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @inheritDoc
     */
    public function saveContact(Contact $contact, bool $flush): void
    {
        $this->save($contact, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeContact(Contact $contact, bool $flush): void
    {
        $this->remove($contact, $flush);
    }

    /**
     * @inheritDoc
     */
    public function createContact(string $nameFirst, string $nameLast, ContactRoleEnum $role, User $user): Contact
    {
        return new Contact($nameFirst, $nameLast, $role, $user);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Contact
    {
        return $this->createQueryBuilder('contact')
            ->select('contact, contactUser')
            ->leftJoin('contact.user', 'contactUser')
            ->andWhere('contact.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(ContactSearchDataInterface $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('contact')
            ->andWhere('CONCAT(contact.nameFirst, \' \', contact.nameLast) LIKE :fullName')
            ->setParameter('fullName', '%' . $phrase . '%')
            ->andWhere('contact.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}