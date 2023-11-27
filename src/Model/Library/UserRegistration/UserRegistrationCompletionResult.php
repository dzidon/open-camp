<?php

namespace App\Model\Library\UserRegistration;

use App\Model\Entity\User;
use App\Model\Entity\UserRegistration;
use LogicException;

/**
 * @inheritDoc
 */
class UserRegistrationCompletionResult implements UserRegistrationCompletionResultInterface
{
    private ?User $user;

    private ?UserRegistration $usedUserRegistration;

    /**
     * @var UserRegistration[]
     */
    private array $disabledUserRegistrations;

    public function __construct(?User             $user = null,
                                ?UserRegistration $usedUserRegistration = null,
                                array             $disabledUserRegistrations = [])
    {
        foreach ($disabledUserRegistrations as $disabledUserRegistration)
        {
            if (!$disabledUserRegistration instanceof UserRegistration)
            {
                throw new LogicException(
                    sprintf("Disabled user registrations passed to the constructor of %s must all be instances of %s.", self::class, UserRegistration::class)
                );
            }
        }

        $this->user = $user;
        $this->usedUserRegistration = $usedUserRegistration;
        $this->disabledUserRegistrations = $disabledUserRegistrations;
    }

    /**
     * @inheritDoc
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getUsedUserRegistration(): ?UserRegistration
    {
        return $this->usedUserRegistration;
    }

    /**
     * @inheritDoc
     */
    public function getDisabledUserRegistrations(): array
    {
        return $this->disabledUserRegistrations;
    }
}