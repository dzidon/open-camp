<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueUser;
use App\Library\Data\User\BillingData;
use App\Model\Entity\Role;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueUser]
class UserData
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    private ?Role $role = null;

    #[AssertPhoneNumber]
    private ?PhoneNumber $leaderPhoneNumber = null;

    #[Assert\Valid]
    private BillingData $billingData;

    public function __construct(bool $isEuBusinessDataEnabled)
    {
        $this->billingData = new BillingData($isEuBusinessDataEnabled);
    }

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function setId(?UuidV4 $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getLeaderPhoneNumber(): ?PhoneNumber
    {
        if ($this->leaderPhoneNumber !== null)
        {
            return clone $this->leaderPhoneNumber;
        }

        return $this->leaderPhoneNumber;
    }

    public function setLeaderPhoneNumber(?PhoneNumber $leaderPhoneNumber): self
    {
        if ($leaderPhoneNumber !== null)
        {
            $leaderPhoneNumber = clone $leaderPhoneNumber;
        }

        $this->leaderPhoneNumber = $leaderPhoneNumber;

        return $this;
    }

    public function getBillingData(): BillingData
    {
        return $this->billingData;
    }
}