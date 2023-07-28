<?php

namespace App\Model\Repository;

use App\Form\DataTransfer\Data\User\ContactSearchDataInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Search\Paginator\DqlPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
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
            ->andWhere('contact.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('contact.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->orderBy('contact.' . $sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}