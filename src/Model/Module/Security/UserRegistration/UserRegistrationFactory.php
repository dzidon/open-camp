<?php

namespace App\Model\Module\Security\UserRegistration;

use App\Model\Entity\UserRegistration;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Security\Hasher\UserRegistrationVerifierHasherInterface;
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
    private UserRegistrationVerifierHasherInterface $verifierHasher;
    private UserRepositoryInterface $userRepository;

    public function __construct(TokenSplitterInterface                  $tokenSplitter,
                                UserRegistrationRepositoryInterface     $userRegistrationRepository,
                                UserRegistrationVerifierHasherInterface $verifierHasher,
                                UserRepositoryInterface                 $userRepository,
                                int                                     $maxActiveRegistrationsPerEmail,
                                string                                  $registrationLifespan)
    {
        $this->tokenSplitter = $tokenSplitter;
        $this->userRegistrationRepository = $userRegistrationRepository;
        $this->verifierHasher = $verifierHasher;
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
        $verifier = $this->verifierHasher->hashVerifier($plainVerifier);
        $userRegistration = new UserRegistration($email, $expireAt, $selector, $verifier);

        // save
        if (!$fake)
        {
            $this->userRegistrationRepository->saveUserRegistration($userRegistration, $flush);
        }

        return new UserRegistrationResult($userRegistration, $plainVerifier, $fake);
    }
}