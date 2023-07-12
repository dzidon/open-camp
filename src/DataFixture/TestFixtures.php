<?php

namespace App\DataFixture;

use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Enum\Entity\UserRegistrationStateEnum;
use App\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\Contact;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;
use App\Model\Entity\UserRegistration;
use App\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Security\Hasher\UserRegistrationVerifierHasherInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fake data used for testing.
 */
class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $userHasher;
    private UserPasswordChangeVerifierHasherInterface $passwordChangeHasher;
    private UserRegistrationVerifierHasherInterface $registrationHasher;
    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(UserPasswordHasherInterface               $userHasher,
                                UserPasswordChangeVerifierHasherInterface $passwordChangeHasher,
                                UserRegistrationVerifierHasherInterface   $registrationHasher,
                                PhoneNumberUtil                           $phoneNumberUtil)
    {
        $this->userHasher = $userHasher;
        $this->passwordChangeHasher = $passwordChangeHasher;
        $this->registrationHasher = $registrationHasher;
        $this->phoneNumberUtil = $phoneNumberUtil;
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
        $permissionGroup2 = new PermissionGroup('group2', 'Group 2', 200);
        $manager->persist($permissionGroup1);
        $manager->persist($permissionGroup2);

        /*
         * Permission
         */
        $permission1 = new Permission('permission1', 'Permission 1', 100, $permissionGroup1);
        $permission2 = new Permission('permission2', 'Permission 2', 200, $permissionGroup1);
        $permission3 = new Permission('permission3', 'Permission 3', 300, $permissionGroup2);
        $permission4 = new Permission('permission4', 'Permission 4', 400, $permissionGroup2);
        $manager->persist($permission1);
        $manager->persist($permission2);
        $manager->persist($permission3);
        $manager->persist($permission4);

        /*
         * Role
         */
        $role1 = new Role('Super admin');
        $role1
            ->addPermission($permission1)
            ->addPermission($permission2)
            ->addPermission($permission3)
            ->addPermission($permission4)
        ;

        $role2 = new Role('Admin');
        $role2->addPermission($permission3);

        $manager->persist($role1);
        $manager->persist($role2);

        /*
         * User
         */
        $user1 = new User('david@gmail.com');
        $user1->setName('David Smith');
        $user1->setRole($role1);
        $manager->persist($user1);

        $user2 = new User('kate@gmail.com');
        $hashedPassword = $this->userHasher->hashPassword($user2, '123456');
        $user2->setPassword($hashedPassword);
        $user2->setRole($role2);
        $manager->persist($user2);

        $user3 = new User('jeff@gmail.com');
        $manager->persist($user3);

        $user4 = new User('xena@gmail.com');
        $manager->persist($user4);

        $user5 = new User('mark@gmail.com');
        $manager->persist($user5);

        /*
         * UserRegistration
         */
        $verifier = $this->registrationHasher->hashVerifier('123');

        // only one active registration
        $userRegistration1 = new UserRegistration('max@gmail.com', $expirationDateFuture, 'max', $verifier);
        $manager->persist($userRegistration1);

        // two active registrations (max amount of active registrations in test env)
        $userRegistration2 = new UserRegistration('roman@gmail.com', $expirationDateFuture, 'ro1', $verifier);
        $userRegistration3 = new UserRegistration('roman@gmail.com', $expirationDateFuture, 'ro2', $verifier);
        $manager->persist($userRegistration2);
        $manager->persist($userRegistration3);

        // one active and one inactive registration (expiration date exceeded)
        $userRegistration4 = new UserRegistration('lucas@gmail.com', $expirationDateFuture, 'lu1', $verifier);
        $userRegistration5 = new UserRegistration('lucas@gmail.com', $expirationDatePast, 'lu2', $verifier);
        $manager->persist($userRegistration4);
        $manager->persist($userRegistration5);

        // one active and one inactive registration (disabled)
        $userRegistration6 = new UserRegistration('tim@gmail.com', $expirationDateFuture, 'ti1', $verifier);
        $userRegistration7 = new UserRegistration('tim@gmail.com', $expirationDateFuture, 'ti2', $verifier);
        $userRegistration7->setState(UserRegistrationStateEnum::DISABLED);
        $manager->persist($userRegistration6);
        $manager->persist($userRegistration7);

        // one active and one inactive registration (used)
        $userRegistration8 = new UserRegistration('alena@gmail.com', $expirationDateFuture, 'al1', $verifier);
        $userRegistration9 = new UserRegistration('alena@gmail.com', $expirationDateFuture, 'al2', $verifier);
        $userRegistration9->setState(UserRegistrationStateEnum::USED);
        $manager->persist($userRegistration8);
        $manager->persist($userRegistration9);

        // edge case - active registrations for an already registered account
        $userRegistration10 = new UserRegistration('kate@gmail.com', $expirationDateFuture, 'ka1', $verifier);
        $userRegistration11 = new UserRegistration('kate@gmail.com', $expirationDateFuture, 'ka2', $verifier);
        $manager->persist($userRegistration10);
        $manager->persist($userRegistration11);

        /*
         * UserPasswordChange
         */
        $verifier = $this->passwordChangeHasher->hashVerifier('123');

        // only one active password change
        $userPasswordChange1 = new UserPasswordChange($expirationDateFuture, 'dav', $verifier);
        $userPasswordChange1->setUser($user1); // david@gmail.com
        $manager->persist($userPasswordChange1);

        // two active password changes (max amount of active password changes in test env)
        $userPasswordChange2 = new UserPasswordChange($expirationDateFuture, 'ka1', $verifier);
        $userPasswordChange2->setUser($user2); // kate@gmail.com
        $userPasswordChange3 = new UserPasswordChange($expirationDateFuture, 'ka2', $verifier);
        $userPasswordChange3->setUser($user2); // kate@gmail.com
        $manager->persist($userPasswordChange2);
        $manager->persist($userPasswordChange3);

        // inactive - no user
        $userPasswordChange4 = new UserPasswordChange($expirationDateFuture, 'xxx', $verifier);
        $manager->persist($userPasswordChange4);

        // one active and one inactive registration (expiration date exceeded)
        $userPasswordChange5 = new UserPasswordChange($expirationDateFuture, 'je1', $verifier);
        $userPasswordChange5->setUser($user3); // jeff@gmail.com
        $userPasswordChange6 = new UserPasswordChange($expirationDatePast, 'je2', $verifier);
        $userPasswordChange6->setUser($user3); // jeff@gmail.com
        $manager->persist($userPasswordChange5);
        $manager->persist($userPasswordChange6);

        // one active and one inactive registration (disabled)
        $userPasswordChange7 = new UserPasswordChange($expirationDateFuture, 'xe1', $verifier);
        $userPasswordChange7->setUser($user4); // xena@gmail.com
        $userPasswordChange8 = new UserPasswordChange($expirationDateFuture, 'xe2', $verifier);
        $userPasswordChange8->setState(UserPasswordChangeStateEnum::DISABLED);
        $userPasswordChange8->setUser($user4); // xena@gmail.com
        $manager->persist($userPasswordChange7);
        $manager->persist($userPasswordChange8);

        // one active and one inactive registration (used)
        $userPasswordChange9 = new UserPasswordChange($expirationDateFuture, 'ma1', $verifier);
        $userPasswordChange9->setUser($user5); // mark@gmail.com
        $userPasswordChange10 = new UserPasswordChange($expirationDateFuture, 'ma2', $verifier);
        $userPasswordChange10->setState(UserPasswordChangeStateEnum::USED);
        $userPasswordChange10->setUser($user5); // mark@gmail.com
        $manager->persist($userPasswordChange9);
        $manager->persist($userPasswordChange10);

        /*
         * Contact
         */
        $phoneNumber = $this->phoneNumberUtil->parse('+420607999888');
        $contact1 = new Contact('David Smith', 'david.smith@gmail.com', $phoneNumber, $user1);
        $manager->persist($contact1);

        $phoneNumber = $this->phoneNumberUtil->parse('+420724999888');
        $contact2 = new Contact('Jessica Smith', 'jess.smith@gmail.com', $phoneNumber, $user1);
        $manager->persist($contact2);

        /*
         * Camper
         */
        $bornAtDate = new DateTimeImmutable('2000-01-01');
        $camper1 = new Camper('Camper 1', GenderEnum::MALE, $bornAtDate, $user1);
        $manager->persist($camper1);

        $camper2 = new Camper('Camper 2', GenderEnum::FEMALE, $bornAtDate, $user1);
        $manager->persist($camper2);

        // save
        $manager->flush();
    }
}
