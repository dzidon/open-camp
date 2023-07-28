<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Form\DataTransfer\Data\User\BillingData;
use App\Model\Entity\Role;
use App\Validator\Constraint as CustomAssert;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
#[CustomAssert\UniqueUser]
class UserData implements UserDataInterface
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    private ?Role $role = null;

    #[Assert\Valid]
    private BillingData $billingData;

    public function __construct()
    {
        $this->billingData = new BillingData();
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

    public function getBillingData(): BillingData
    {
        return $this->billingData;
    }
}