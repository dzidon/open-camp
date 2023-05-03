<?php

namespace App\DataFixture;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fake data used for testing.
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        // users
        $user1 = new User('david@gmail.com');
        $manager->persist($user1);

        $user2 = new User('alice@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($user2, '123456');
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);

        $manager->flush();
    }
}
