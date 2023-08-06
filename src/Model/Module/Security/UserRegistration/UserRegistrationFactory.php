<?php

namespace App\Model\Module\Security\UserRegistration;

use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Security\Token\TokenSplitterInterface;
use DateTimeImmutable;

/**
 * @inheritDoc
 */
class UserRegistrationFactory implements UserRegistrationFactoryInterface
{
    private int $maxActiveRegistrationsPerEmail;
    private string $registrationLifespan;

    private TokenSplitterInterface $tokenSplitter;
    private UserRegistrationRepositoryInterface $userRegistrationRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(TokenSplitterInterface $tokenSplitter,
                                UserRegistrationRepositoryInterface $userRegistrationRepository,
                                UserRepositoryInterface $userRepository,
                                int $maxActiveRegistrationsPerEmail,
                                string $registrationLifespan)
    {
        $this->tokenSplitter = $tokenSplitter;
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->userRepository = $userRepository;

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

        // save
        if (!$fake)
        {
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, $flush);
        }

        return new UserRegistrationResult($userRegistration, $plainVerifier, $fake);
    }
}