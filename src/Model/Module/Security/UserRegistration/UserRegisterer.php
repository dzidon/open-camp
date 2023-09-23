<?php

namespace App\Model\Module\Security\UserRegistration;

use App\Model\Entity\User;
use App\Model\Entity\UserRegistration;
use App\Model\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserRegisterer implements UserRegistererInterface
{
    private UserRegistrationRepositoryInterface $userRegistrationRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserPasswordHasherInterface         $passwordHasher,
                                UserRepositoryInterface             $userRepository)
    {
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword, bool $flush): void
    {
        if (!$userRegistration->isActive())
        {
            return;
        }

        $email = $userRegistration->getEmail();
        $otherEmailRegistrations = $this->userRegistrationRepository->findByEmail($email, true);

        foreach ($otherEmailRegistrations as $otherRegistration)
        {
            if ($userRegistration->getId()->toRfc4122() === $otherRegistration->getId()->toRfc4122())
            {
                continue;
            }

            $otherRegistration->setState(UserRegistrationStateEnum::DISABLED);
            $this->userRegistrationRepository->saveUserRegistration($otherRegistration, false);
        }

        if ($this->userRepository->isEmailRegistered($email))
        {
            $userRegistration->setState(UserRegistrationStateEnum::DISABLED);
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, $flush);
        }
        else
        {
            $userRegistration->setState(UserRegistrationStateEnum::USED);
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, false);

            $user = new User($email);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $this->userRepository->saveUser($user, $flush);
        }
    }
}