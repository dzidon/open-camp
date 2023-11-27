<?php

namespace App\Model\Library\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use LogicException;

/**
 * @inheritDoc
 */
class UserPasswordChangeCompletionResult implements UserPasswordChangeCompletionResultInterface
{
    private ?UserPasswordChange $usedUserPasswordChange;

    /**
     * @var UserPasswordChange[]
     */
    private array $disabledUserPasswordChanges;

    public function __construct(?UserPasswordChange $usedUserPasswordChange = null, array $disabledUserPasswordChanges = [])
    {
        foreach ($disabledUserPasswordChanges as $disabledUserPasswordChange)
        {
            if (!$disabledUserPasswordChange instanceof UserPasswordChange)
            {
                throw new LogicException(
                    sprintf("Disabled user password changes passed to the constructor of %s must all be instances of %s.", self::class, UserPasswordChange::class)
                );
            }
        }

        $this->usedUserPasswordChange = $usedUserPasswordChange;
        $this->disabledUserPasswordChanges = $disabledUserPasswordChanges;
    }

    /**
     * @inheritDoc
     */
    public function getUsedUserPasswordChange(): ?UserPasswordChange
    {
        return $this->usedUserPasswordChange;
    }

    /**
     * @inheritDoc
     */
    public function getDisabledUserPasswordChanges(): array
    {
        return $this->disabledUserPasswordChanges;
    }
}