<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\DataTransfer\Data\User\ContactSearchDataInterface;
use App\Search\Paginator\DqlPaginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends AbstractRepository implements ContactRepositoryInterface
{
    private string $phoneNumberDefaultLocale;

    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(ManagerRegistry $registry, PhoneNumberUtil $phoneNumberUtil, string $phoneNumberDefaultLocale)
    {
        parent::__construct($registry, Contact::class);

        $this->phoneNumberUtil = $phoneNumberUtil;

        $this->phoneNumberDefaultLocale = $phoneNumberDefaultLocale;
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
    public function createContact(string $name, string $email, PhoneNumber|string $phoneNumber, User $user): Contact
    {
        if (is_string($phoneNumber))
        {
            $phoneNumber = $this->phoneNumberUtil->parse($phoneNumber, $this->phoneNumberDefaultLocale);
        }

        return new Contact($name, $email, $phoneNumber, $user);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->select('c, cu')
            ->leftJoin('c.user', 'cu')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(ContactSearchDataInterface $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('c')
            ->andWhere('c.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.' . $sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query), $currentPage, $pageSize);
    }
}