<?php

namespace App\Model\Module\Security\UserPasswordChange;

use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Security\Token\TokenSplitterInterface;
use DateTimeImmutable;

/**
 * @inheritDoc
 */
class UserPasswordChangeFactory implements UserPasswordChangeFactoryInterface
{
    private int $maxActivePasswordChangesPerUser;
    private string $passwordChangeLifespan;

    private TokenSplitterInterface $tokenSplitter;
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(TokenSplitterInterface $tokenSplitter,
                                UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserRepositoryInterface $userRepository,
                                int $maxActivePasswordChangesPerUser,
                                string $passwordChangeLifespan)
    {
        $this->tokenSplitter = $tokenSplitter;
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userRepository = $userRepository;

        $this->maxActivePasswordChangesPerUser = $maxActivePasswordChangesPerUser;
        $this->passwordChangeLifespan = $passwordChangeLifespan;
    }

    /**
     * @inheritDoc
     */
    public function createUserPasswordChange(string $email, bool $flush): UserPasswordChangeResult
    {
        $fake = false;

        // this email might not be registered
        $user = $this->userRepository->findOneByEmail($email);
        if ($user === null)
        {
            $fake = true;
        }
        else
        {
            // maximum amount of active password changes might have been reached
            $activePasswordChanges = $this->userPasswordChangeRepository->findByUser($user, true);
            if (count($activePasswordChanges) >= $this->maxActivePasswordChangesPerUser)
            {
                $fake = true;
            }
        }

        // make sure the selector is unique
        $selector = null;
        $plainVerifier = '';

        while ($selector === null || $this->userPasswordChangeRepository->findOneBySelector($selector) !== null)
        {
            $tokenSplit = $this->tokenSplitter->generateTokenSplit();
            $selector = $tokenSplit->getSelector();
            $plainVerifier = $tokenSplit->getPlainVerifier();
        }

        // create a password change
        $expireAt = new DateTimeImmutable(sprintf('+%s', $this->passwordChangeLifespan));
        $userPasswordChange = $this->userPasswordChangeRepository->createUserPasswordChange(
            $expireAt, $selector, $plainVerifier
        );

        // assign user
        if ($user !== null)
        {
            $userPasswordChange->setUser($user);
        }

        // save
        if (!$fake)
        {
            $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, $flush);
        }

        return new UserPasswordChangeResult($userPasswordChange, $plainVerifier, $fake);
    }
}