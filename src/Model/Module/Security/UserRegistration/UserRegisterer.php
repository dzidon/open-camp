<?php

namespace App\Model\Module\Security\UserRegistration;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Entity\UserRegistration;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;

/**
 * @inheritDoc
 */
class UserRegisterer implements UserRegistererInterface
{
    private UserRegistrationRepositoryInterface $userRegistrationRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->userRegistrationRepository = $userRegistrationRepository;
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
            if ($userRegistration->getId() === $otherRegistration->getId())
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

            $user = $this->userRepository->createUser($email, $plainPassword);
            $this->userRepository->saveUser($user, $flush);
        }
    }
}