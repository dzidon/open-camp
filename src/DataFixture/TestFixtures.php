<?php

namespace App\DataFixture;

use App\Entity\User;
use App\Entity\UserRegistration;
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

        /*
         * UserRegistration
         */
        $userRegistrationHasher = $this->passwordHasher->getPasswordHasher(UserRegistration::class);
        $verifier = $userRegistrationHasher->hash('123');

        // only one active registration
        $userRegistration1 = new UserRegistration('max@gmail.com', $expirationDateFuture, 'max', $verifier);
        $manager->persist($userRegistration1);

        // two active registrations (max amount of inactive registrations in test env)
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

        $manager->flush();
    }
}
