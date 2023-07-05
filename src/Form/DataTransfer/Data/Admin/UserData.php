<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Entity\Role;
use App\Form\Type\Admin\UserType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

/**
 * See {@link UserType}
 */
#[CustomAssert\UniqueUserData]
class UserData implements UserDataInterface
{
    /** @var int|null Used for the unique constraint. */
    private ?int $id = null;

    #[Assert\Length(max: 180)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email = '';

    private ?Role $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = (string) $email;

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
}