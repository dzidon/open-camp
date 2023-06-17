<?php

namespace App\Security;

use App\Entity\UserPasswordChange;
use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Repository\UserPasswordChangeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserPasswordChanger implements UserPasswordChangerInterface
{
    private int $maxActivePasswordChangesPerUser;
    private string $passwordChangeLifespan;

    private TokenSplitterInterface $tokenSplitter;
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private PasswordHasherFactoryInterface $passwordHasher;

    public function __construct(TokenSplitterInterface $tokenSplitter,
                                UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserRepositoryInterface $userRepository,
                                UserPasswordHasherInterface $userPasswordHasher,
                                PasswordHasherFactoryInterface $passwordHasher,
                                int $maxActivePasswordChangesPerUser,
                                string $passwordChangeLifespan)
    {
        $this->tokenSplitter = $tokenSplitter;
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->passwordHasher = $passwordHasher;

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

    /**
     * @inheritDoc
     */
    public function verify(UserPasswordChange $userPasswordChange, string $plainVerifier): bool
    {
        $hasher = $this->passwordHasher->getPasswordHasher(UserPasswordChange::class);
        $verifier = $userPasswordChange->getVerifier();

        return $hasher->verify($verifier, $plainVerifier);
    }

    /**
     * @inheritDoc
     */
    public function completeUserPasswordChange(UserPasswordChange $userPasswordChange, string $plainPassword, bool $flush): void
    {
        if (!$userPasswordChange->isActive())
        {
            return;
        }

        $user = $userPasswordChange->getUser();
        $otherPasswordChanges = $this->userPasswordChangeRepository->findByUser($user, true);

        // mark other user's active password changes as disabled
        foreach ($otherPasswordChanges as $otherPasswordChange)
        {
            if ($userPasswordChange->getId() === $otherPasswordChange->getId())
            {
                continue;
            }

            $otherPasswordChange->setState(UserPasswordChangeStateEnum::DISABLED);
            $this->userPasswordChangeRepository->saveUserPasswordChange($otherPasswordChange, false);
        }

        // mark the current password change as used
        $userPasswordChange->setState(UserPasswordChangeStateEnum::USED);
        $this->userPasswordChangeRepository->saveUserPasswordChange($userPasswordChange, false);

        // set new user password
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);

        $this->userRepository->saveUser($user, $flush);
    }
}