<?php

namespace App\Model\DataFixture;

use App\Library\Enum\GenderEnum;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Model\Entity\CampDate;
use App\Model\Entity\Camper;
use App\Model\Entity\CampImage;
use App\Model\Entity\Contact;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;
use App\Model\Entity\UserRegistration;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Enum\Entity\UserRegistrationStateEnum;
use App\Service\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Service\Security\Hasher\UserRegistrationVerifierHasherInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * Fake data used for testing.
 */
class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $userHasher;
    private UserPasswordChangeVerifierHasherInterface $passwordChangeHasher;
    private UserRegistrationVerifierHasherInterface $registrationHasher;

    public function __construct(UserPasswordHasherInterface               $userHasher,
                                UserPasswordChangeVerifierHasherInterface $passwordChangeHasher,
                                UserRegistrationVerifierHasherInterface   $registrationHasher)
    {
        $this->userHasher = $userHasher;
        $this->passwordChangeHasher = $passwordChangeHasher;
        $this->registrationHasher = $registrationHasher;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $expirationDateFuture = new DateTimeImmutable('3000-01-01 12:00:00');
        $expirationDatePast = new DateTimeImmutable('2000-01-01 12:00:00');

        /*
         * PermissionGroup
         */
        $permissionGroup1 = new PermissionGroup('group1', 'Group 1', 100);
        $this->setUid($permissionGroup1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($permissionGroup1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($permissionGroup1);

        $permissionGroup2 = new PermissionGroup('group2', 'Group 2', 200);
        $this->setCreatedAt($permissionGroup2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($permissionGroup2);

        /*
         * Permission
         */
        $permission1 = new Permission('permission1', 'Permission 1', 100, $permissionGroup1);
        $this->setUid($permission1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($permission1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($permission1);

        $permission2 = new Permission('permission2', 'Permission 2', 200, $permissionGroup1);
        $this->setCreatedAt($permission2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($permission2);

        $permission3 = new Permission('permission3', 'Permission 3', 300, $permissionGroup2);
        $this->setCreatedAt($permission3, new DateTimeImmutable('2000-01-03'));
        $manager->persist($permission3);

        $permission4 = new Permission('permission4', 'Permission 4', 400, $permissionGroup2);
        $this->setCreatedAt($permission4, new DateTimeImmutable('2000-01-04'));
        $manager->persist($permission4);

        /*
         * Role
         */
        $role1 = new Role('Super admin');
        $this->setUid($role1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($role1, new DateTimeImmutable('2000-01-01'));
        $role1
            ->addPermission($permission1)
            ->addPermission($permission2)
            ->addPermission($permission3)
            ->addPermission($permission4)
        ;
        $manager->persist($role1);

        $role2 = new Role('Admin');
        $this->setCreatedAt($role2, new DateTimeImmutable('2000-01-02'));
        $role2->addPermission($permission3);
        $manager->persist($role2);

        /*
         * User
         */
        $user1 = new User('david@gmail.com');
        $this->setUid($user1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($user1, new DateTimeImmutable('2000-01-01'));
        $user1->setNameFirst('David');
        $user1->setNameLast('Smith');
        $user1->setRole($role1);
        $user1->setLastActiveAt(new DateTimeImmutable('1995-01-01'));
        $manager->persist($user1);

        $user2 = new User('kate@gmail.com');
        $this->setCreatedAt($user2, new DateTimeImmutable('2000-01-02'));
        $user2->setNameFirst('Kate');
        $user2->setNameLast('Doe');
        $hashedPassword = $this->userHasher->hashPassword($user2, '123456');
        $user2->setPassword($hashedPassword);
        $user2->setRole($role2);
        $user2->setLastActiveAt(new DateTimeImmutable('1995-01-02'));
        $manager->persist($user2);

        $user3 = new User('jeff@gmail.com');
        $this->setCreatedAt($user3, new DateTimeImmutable('2000-01-03'));
        $user3->setNameFirst('Jeff');
        $user3->setNameLast('Brooks');
        $user3->setLastActiveAt(new DateTimeImmutable('1995-01-03'));
        $manager->persist($user3);

        $user4 = new User('xena@gmail.com');
        $this->setCreatedAt($user4, new DateTimeImmutable('2000-01-04'));
        $user4->setNameFirst('Xena');
        $user4->setNameLast('Rich');
        $user4->setLastActiveAt(new DateTimeImmutable('1995-01-04'));
        $manager->persist($user4);

        $user5 = new User('mark@gmail.com');
        $this->setCreatedAt($user5, new DateTimeImmutable('2000-01-05'));
        $user5->setNameFirst('Mark');
        $user5->setNameLast('Zuckerberg');
        $manager->persist($user5);

        /*
         * UserRegistration
         */
        $verifier = $this->registrationHasher->hashVerifier('123');

        // only one active registration
        $userRegistration1 = new UserRegistration('max@gmail.com', $expirationDateFuture, 'max', $verifier);
        $this->setUid($userRegistration1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($userRegistration1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($userRegistration1);

        // two active registrations (max amount of active registrations in test env)
        $userRegistration2 = new UserRegistration('roman@gmail.com', $expirationDateFuture, 'ro1', $verifier);
        $this->setCreatedAt($userRegistration2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($userRegistration2);

        $userRegistration3 = new UserRegistration('roman@gmail.com', $expirationDateFuture, 'ro2', $verifier);
        $this->setCreatedAt($userRegistration3, new DateTimeImmutable('2000-01-03'));
        $manager->persist($userRegistration3);

        // one active and one inactive registration (expiration date exceeded)
        $userRegistration4 = new UserRegistration('lucas@gmail.com', $expirationDateFuture, 'lu1', $verifier);
        $this->setCreatedAt($userRegistration4, new DateTimeImmutable('2000-01-04'));
        $manager->persist($userRegistration4);

        $userRegistration5 = new UserRegistration('lucas@gmail.com', $expirationDatePast, 'lu2', $verifier);
        $this->setCreatedAt($userRegistration5, new DateTimeImmutable('2000-01-05'));
        $manager->persist($userRegistration5);

        // one active and one inactive registration (disabled)
        $userRegistration6 = new UserRegistration('tim@gmail.com', $expirationDateFuture, 'ti1', $verifier);
        $this->setCreatedAt($userRegistration6, new DateTimeImmutable('2000-01-06'));
        $manager->persist($userRegistration6);

        $userRegistration7 = new UserRegistration('tim@gmail.com', $expirationDateFuture, 'ti2', $verifier);
        $this->setCreatedAt($userRegistration7, new DateTimeImmutable('2000-01-07'));
        $userRegistration7->setState(UserRegistrationStateEnum::DISABLED);
        $manager->persist($userRegistration7);

        // one active and one inactive registration (used)
        $userRegistration8 = new UserRegistration('alena@gmail.com', $expirationDateFuture, 'al1', $verifier);
        $this->setCreatedAt($userRegistration8, new DateTimeImmutable('2000-01-08'));
        $manager->persist($userRegistration8);

        $userRegistration9 = new UserRegistration('alena@gmail.com', $expirationDateFuture, 'al2', $verifier);
        $this->setCreatedAt($userRegistration9, new DateTimeImmutable('2000-01-09'));
        $userRegistration9->setState(UserRegistrationStateEnum::USED);
        $manager->persist($userRegistration9);

        // edge case - active registrations for an already registered account
        $userRegistration10 = new UserRegistration('kate@gmail.com', $expirationDateFuture, 'ka1', $verifier);
        $this->setCreatedAt($userRegistration10, new DateTimeImmutable('2000-01-10'));
        $manager->persist($userRegistration10);

        $userRegistration11 = new UserRegistration('kate@gmail.com', $expirationDateFuture, 'ka2', $verifier);
        $this->setCreatedAt($userRegistration11, new DateTimeImmutable('2000-01-11'));
        $manager->persist($userRegistration11);

        /*
         * UserPasswordChange
         */
        $verifier = $this->passwordChangeHasher->hashVerifier('123');

        // only one active password change
        $userPasswordChange1 = new UserPasswordChange($expirationDateFuture, 'dav', $verifier);
        $this->setUid($userPasswordChange1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($userPasswordChange1, new DateTimeImmutable('2000-01-01'));
        $userPasswordChange1->setUser($user1); // david@gmail.com
        $manager->persist($userPasswordChange1);

        // two active password changes (max amount of active password changes in test env)
        $userPasswordChange2 = new UserPasswordChange($expirationDateFuture, 'ka1', $verifier);
        $this->setCreatedAt($userPasswordChange2, new DateTimeImmutable('2000-01-02'));
        $userPasswordChange2->setUser($user2); // kate@gmail.com
        $manager->persist($userPasswordChange2);

        $userPasswordChange3 = new UserPasswordChange($expirationDateFuture, 'ka2', $verifier);
        $this->setCreatedAt($userPasswordChange3, new DateTimeImmutable('2000-01-03'));
        $userPasswordChange3->setUser($user2); // kate@gmail.com
        $manager->persist($userPasswordChange3);

        // inactive - no user
        $userPasswordChange4 = new UserPasswordChange($expirationDateFuture, 'xxx', $verifier);
        $this->setCreatedAt($userPasswordChange4, new DateTimeImmutable('2000-01-04'));
        $manager->persist($userPasswordChange4);

        // one active and one inactive registration (expiration date exceeded)
        $userPasswordChange5 = new UserPasswordChange($expirationDateFuture, 'je1', $verifier);
        $this->setCreatedAt($userPasswordChange5, new DateTimeImmutable('2000-01-05'));
        $userPasswordChange5->setUser($user3); // jeff@gmail.com
        $manager->persist($userPasswordChange5);

        $userPasswordChange6 = new UserPasswordChange($expirationDatePast, 'je2', $verifier);
        $this->setCreatedAt($userPasswordChange6, new DateTimeImmutable('2000-01-06'));
        $userPasswordChange6->setUser($user3); // jeff@gmail.com
        $manager->persist($userPasswordChange6);

        // one active and one inactive registration (disabled)
        $userPasswordChange7 = new UserPasswordChange($expirationDateFuture, 'xe1', $verifier);
        $this->setCreatedAt($userPasswordChange7, new DateTimeImmutable('2000-01-07'));
        $userPasswordChange7->setUser($user4); // xena@gmail.com
        $manager->persist($userPasswordChange7);

        $userPasswordChange8 = new UserPasswordChange($expirationDateFuture, 'xe2', $verifier);
        $this->setCreatedAt($userPasswordChange8, new DateTimeImmutable('2000-01-08'));
        $userPasswordChange8->setState(UserPasswordChangeStateEnum::DISABLED);
        $userPasswordChange8->setUser($user4); // xena@gmail.com
        $manager->persist($userPasswordChange8);

        // one active and one inactive registration (used)
        $userPasswordChange9 = new UserPasswordChange($expirationDateFuture, 'ma1', $verifier);
        $this->setCreatedAt($userPasswordChange9, new DateTimeImmutable('2000-01-09'));
        $userPasswordChange9->setUser($user5); // mark@gmail.com
        $manager->persist($userPasswordChange9);

        $userPasswordChange10 = new UserPasswordChange($expirationDateFuture, 'ma2', $verifier);
        $this->setCreatedAt($userPasswordChange10, new DateTimeImmutable('2000-01-10'));
        $userPasswordChange10->setState(UserPasswordChangeStateEnum::USED);
        $userPasswordChange10->setUser($user5); // mark@gmail.com
        $manager->persist($userPasswordChange10);

        /*
         * Contact
         */
        $contact1 = new Contact('David', 'Smith', ContactRoleEnum::FATHER, $user1);
        $this->setUid($contact1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($contact1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($contact1);

        $contact2 = new Contact('Jessica', 'Smith', ContactRoleEnum::MOTHER, $user1);
        $this->setCreatedAt($contact2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($contact2);

        /*
         * Camper
         */
        $bornAtDate = new DateTimeImmutable('2000-01-01');
        $camper1 = new Camper('Camper', '1', GenderEnum::MALE, $bornAtDate, $user1);
        $this->setUid($camper1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($camper1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($camper1);

        $camper2 = new Camper('Camper', '2', GenderEnum::FEMALE, $bornAtDate, $user1);
        $this->setCreatedAt($camper2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($camper2);

        /*
         * CampCategory
         */
        $campCategory1 = new CampCategory('Category 1', 'category-1');
        $this->setUid($campCategory1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($campCategory1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($campCategory1);

        $campCategory2 = new CampCategory('Category 2', 'category-2');
        $this->setCreatedAt($campCategory2, new DateTimeImmutable('2000-01-02'));
        $campCategory2->setParent($campCategory1);
        $manager->persist($campCategory2);

        $campCategory3 = new CampCategory('Category 3', 'category-3');
        $this->setCreatedAt($campCategory3, new DateTimeImmutable('2000-01-03'));
        $manager->persist($campCategory3);

        /*
         * Camp
         */
        $camp1 = new Camp('Camp 1', 'camp-1', 6, 12, 'Street 123', 'Town 1', '12345', 'CS');
        $this->setUid($camp1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camp1->setCampCategory($campCategory1);
        $this->setCreatedAt($camp1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($camp1);

        $camp2 = new Camp('Camp 2', 'camp-2', 13, 18, 'Street 321', 'Town 2', '54321', 'SK');
        $this->setUid($camp2, 'a08f6f48-3a52-40db-b031-5eb3a468c57a');
        $camp2->setFeaturedPriority(100);
        $this->setCreatedAt($camp2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($camp2);

        /*
         * CampDate
         */
        $campDate1 = new CampDate(new DateTimeImmutable('2000-07-01'), new DateTimeImmutable('2000-07-07'), 1000, 10, $camp1);
        $this->setUid($campDate1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($campDate1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($campDate1);

        $campDate2 = new CampDate(new DateTimeImmutable('3000-07-08'), new DateTimeImmutable('3000-07-14'), 2000, 20, $camp1);
        $this->setUid($campDate2, '550e8400-e29b-41d4-a716-446655440000');
        $this->setCreatedAt($campDate2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($campDate2);

        $campDate3 = new CampDate(new DateTimeImmutable('4000-01-05'), new DateTimeImmutable('4000-01-10'), 3000, 30, $camp2);
        $this->setUid($campDate3, 'c097941e-52c4-405a-9823-49b7b71ead6e');
        $this->setCreatedAt($campDate3, new DateTimeImmutable('2000-01-03'));
        $manager->persist($campDate3);

        /*
         * CampImage
         */
        $campImage1 = new CampImage(100, 'jpg', $camp1);
        $this->setUid($campImage1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($campImage1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($campImage1);

        $campImage2 = new CampImage(200, 'png', $camp1);
        $this->setUid($campImage2, '550e8400-e29b-41d4-a716-446655440000');
        $this->setCreatedAt($campImage2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($campImage2);

        /*
         * TripLocationPath
         */
        $tripLocationPath1 = new TripLocationPath('Path 1');
        $this->setUid($tripLocationPath1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($tripLocationPath1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($tripLocationPath1);

        $tripLocationPath2 = new TripLocationPath('Path 2');
        $this->setCreatedAt($tripLocationPath2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($tripLocationPath2);

        /*
         * TripLocation
         */
        $tripLocation1 = new TripLocation('Location 1', 1000.0, 200, $tripLocationPath1);
        $this->setUid($tripLocation1, 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $this->setCreatedAt($tripLocation1, new DateTimeImmutable('2000-01-01'));
        $manager->persist($tripLocation1);

        $tripLocation2 = new TripLocation('Location 2', 2000.0, 100, $tripLocationPath1);
        $this->setCreatedAt($tripLocation2, new DateTimeImmutable('2000-01-02'));
        $manager->persist($tripLocation2);

        $tripLocation3 = new TripLocation('Location 3', 300.0, 100, $tripLocationPath2);
        $this->setCreatedAt($tripLocation3, new DateTimeImmutable('2000-01-03'));
        $manager->persist($tripLocation3);

        // save
        $manager->flush();
    }

    private function setCreatedAt(object $entity, DateTimeInterface $dateTime): void
    {
        $reflectionClass = new ReflectionClass($entity);
        $property = $reflectionClass->getProperty('createdAt');
        $property->setValue($entity, $dateTime);
    }

    private function setUid(object $entity, string $uidString): void
    {
        $reflectionClass = new ReflectionClass($entity);
        $property = $reflectionClass->getProperty('id');
        $property->setValue($entity, UuidV4::fromString($uidString));
    }
}
