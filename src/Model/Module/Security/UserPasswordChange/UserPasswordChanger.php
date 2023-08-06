<?php

namespace App\Model\Module\Security\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserPasswordChanger implements UserPasswordChangerInterface
{
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserRepositoryInterface $userRepository,
                                UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
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
            if ($userPasswordChange->getId()->toRfc4122() === $otherPasswordChange->getId()->toRfc4122())
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