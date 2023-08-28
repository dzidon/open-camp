<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueRole;
use App\Model\Entity\Permission;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueRole]
class RoleData
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @var Permission[]
     */
    private array $permissions = [];

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function setId(?UuidV4 $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (in_array($permission, $this->permissions))
        {
            return $this;
        }

        $this->permissions[] = $permission;

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $key = array_search($permission, $this->permissions, true);

        if ($key === false)
        {
            return $this;
        }

        unset($this->permissions[$key]);

        return $this;
    }
}