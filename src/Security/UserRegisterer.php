<?php

namespace App\Security;

use App\Entity\UserRegistration;
use App\Enum\Entity\UserRegistrationStateEnum;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @inheritDoc
 */
class UserRegisterer implements UserRegistererInterface
{
    private int $maxActiveRegistrationsPerEmail;
    private string $registrationLifespan;

    private TokenSplitterInterface $tokenSplitter;
    private UserRegistrationRepositoryInterface $userRegistrationRepository;
    private UserRepositoryInterface $userRepository;
    private PasswordHasherFactoryInterface $passwordHasher;

    public function __construct(TokenSplitterInterface $tokenSplitter,
                                UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface $userRepository,
                                PasswordHasherFactoryInterface $passwordHasher,
                                int $maxActiveRegistrationsPerEmail,
                                string $registrationLifespan)
    {
        $this->tokenSplitter = $tokenSplitter;
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;

        $this->maxActiveRegistrationsPerEmail = $maxActiveRegistrationsPerEmail;
        $this->registrationLifespan = $registrationLifespan;
    }

    /**
     * @inheritDoc
     */
    public function createUserRegistration(string $email, bool $flush): UserRegistrationResult
    {
        $fake = false;

        // this email might be registered already
        if ($this->userRepository->isEmailRegistered($email))
        {
            $fake = true;
        }

        // maximum amount of active registrations might have been reached
        $activeRegistrations = $this->userRegistrationRepository->findByEmail($email, true);
        if (count($activeRegistrations) >= $this->maxActiveRegistrationsPerEmail)
        {
            $fake = true;
        }

        // make sure the selector is unique
        $selector = null;
        $plainVerifier = '';

        while ($selector === null || $this->userRegistrationRepository->findOneBySelector($selector) !== null)
        {
            $tokenSplit = $this->tokenSplitter->generateTokenSplit();
            $selector = $tokenSplit->getSelector();
            $plainVerifier = $tokenSplit->getPlainVerifier();
        }

        // create a registration and return the result
        $expireAt = new DateTimeImmutable(sprintf('+%s', $this->registrationLifespan));
        $userRegistration = $this->userRegistrationRepository->createUserRegistration(
            $email, $expireAt, $selector, $plainVerifier
        );

        $this->userRegistrationRepository->saveUserRegistration($userRegistration, $flush && !$fake);

        return new UserRegistrationResult($userRegistration, $plainVerifier, $fake);
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