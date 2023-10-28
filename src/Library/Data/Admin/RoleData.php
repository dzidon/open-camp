<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueRole;
use App\Model\Entity\Permission;
use App\Model\Entity\Role;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueRole]
class RoleData
{
    private ?Role $role;

    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @var Permission[]
     */
    private array $permissions = [];

    public function __construct(?Role $role = null)
    {
        $this->role = $role;
    }

    public function getRole(): ?Role
    {
        return $this->role;
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