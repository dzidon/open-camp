<?php

namespace App\DataFixture;

use App\Entity\User;
use App\Entity\UserPasswordChange;
use App\Entity\UserRegistration;
use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Enum\Entity\UserRegistrationStateEnum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fake data used for testing.
 */
class TestFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private PasswordHasherFactoryInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, PasswordHasherFactoryInterface $passwordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $expirationDateFuture = new DateTimeImmutable('3000-01-01 12:00:00');
        $expirationDatePast = new DateTimeImmutable('2000-01-01 12:00:00');

        /*
         * User
         */
        $user1 = new User('david@gmail.com');
        $manager->persist($user1);

        $user2 = new User('kate@gmail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user2, '123456');
        $user2->setPassword($hashedPassword);
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
        $userRegistrationHasher = $this->passwordHasher->getPasswordHasher(UserRegistration::class);
        $verifier = $userRegistrationHasher->hash('123');

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
        $userPasswordChangeHasher = $this->passwordHasher->getPasswordHasher(UserPasswordChange::class);
        $verifier = $userPasswordChangeHasher->hash('123');

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

        $manager->flush();
    }
}
