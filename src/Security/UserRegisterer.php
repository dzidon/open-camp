<?php

namespace App\Security;

use App\Entity\UserRegistration;
use App\Enum\Entity\UserRegistrationStateEnum;
use App\Mailer\UserRegistrationMailerInterface;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @inheritDoc
 */
class UserRegisterer implements UserRegistererInterface
{
    private UserRegistrationRepositoryInterface $userRegistrationRepository;
    private UserRepositoryInterface $userRepository;
    private PasswordHasherFactoryInterface $passwordHasher;
    private UserRegistrationMailerInterface $mailer;

    public function __construct(UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface $userRepository,
                                PasswordHasherFactoryInterface $passwordHasher,
                                UserRegistrationMailerInterface $mailer)
    {
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function createUserRegistration(string $email): void
    {
        // create user registration
        $result = $this->userRegistrationRepository->createUserRegistration($email);

        // extract data
        $userRegistration = $result->getUserRegistration();
        $fake = $result->isFake();
        $plainVerifier = $result->getPlainVerifier();
        $selector = $userRegistration->getSelector();
        $expireAt = $userRegistration->getExpireAt();

        // email
        $token = sprintf('%s%s', $selector, $plainVerifier);
        $this->mailer->sendEmail($email, $token, $expireAt, $fake);

        if (!$fake)
        {
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, true);
        }
    }

    /**
     * @inheritDoc
     */
    public function verify(UserRegistration $userRegistration, string $plainVerifier): bool
    {
        $hasher = $this->passwordHasher->getPasswordHasher(UserRegistration::class);
        $verifier = $userRegistration->getVerifier();

        return $hasher->verify($verifier, $plainVerifier);
    }

    /**
     * @inheritDoc
     */
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword): void
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
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, true);
        }
        else
        {
            $userRegistration->setState(UserRegistrationStateEnum::USED);
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, false);

            $user = $this->userRepository->createUser($email, $plainPassword);
            $this->userRepository->saveUser($user, true);
        }
    }
}