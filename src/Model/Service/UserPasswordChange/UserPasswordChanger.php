<?php

namespace App\Model\Service\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResult;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserPasswordChanger implements UserPasswordChangerInterface
{
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordChangeRepositoryInterface $userPasswordChangeRepository,
                                UserPasswordHasherInterface           $userPasswordHasher)
    {
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @inheritDoc
     */
    public function completeUserPasswordChange(UserPasswordChange $userPasswordChange, string $plainPassword): UserPasswordChangeCompletionResult
    {
        if (!$userPasswordChange->isActive())
        {
            return new UserPasswordChangeCompletionResult();
        }

        $user = $userPasswordChange->getUser();
        $otherPasswordChanges = $this->userPasswordChangeRepository->findByUser($user, true);

        $disabledPasswordChanges = [];

        // mark other user's active password changes as disabled
        foreach ($otherPasswordChanges as $otherPasswordChange)
        {
            if ($userPasswordChange->getId()->toRfc4122() === $otherPasswordChange->getId()->toRfc4122())
            {
                continue;
            }

            $otherPasswordChange->setState(UserPasswordChangeStateEnum::DISABLED);
            $disabledPasswordChanges[] = $otherPasswordChange;
        }

        // mark the current password change as used
        $userPasswordChange->setState(UserPasswordChangeStateEnum::USED);

        // set new user password
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);

        return new UserPasswordChangeCompletionResult($userPasswordChange, $disabledPasswordChanges);
    }
}