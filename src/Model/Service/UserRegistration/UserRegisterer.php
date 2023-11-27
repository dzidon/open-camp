<?php

namespace App\Model\Service\UserRegistration;

use App\Model\Entity\User;
use App\Model\Entity\UserRegistration;
use App\Model\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Library\UserRegistration\UserRegistrationCompletionResult;
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
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword): UserRegistrationCompletionResult
    {
        if (!$userRegistration->isActive())
        {
            return new UserRegistrationCompletionResult();
        }

        $email = $userRegistration->getEmail();
        $otherEmailRegistrations = $this->userRegistrationRepository->findByEmail($email, true);

        $user = null;
        $usedUserRegistration = null;
        $disabledUserRegistrations = [];

        foreach ($otherEmailRegistrations as $otherRegistration)
        {
            if ($userRegistration->getId()->toRfc4122() === $otherRegistration->getId()->toRfc4122())
            {
                continue;
            }

            $otherRegistration->setState(UserRegistrationStateEnum::DISABLED);
            $disabledUserRegistrations[] = $otherRegistration;
        }

        if ($this->userRepository->isEmailRegistered($email))
        {
            $userRegistration->setState(UserRegistrationStateEnum::DISABLED);
            $disabledUserRegistrations[] = $userRegistration;
        }
        else
        {
            $userRegistration->setState(UserRegistrationStateEnum::USED);
            $usedUserRegistration = $userRegistration;

            $user = new User($email);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        return new UserRegistrationCompletionResult($user, $usedUserRegistration, $disabledUserRegistrations);
    }
}