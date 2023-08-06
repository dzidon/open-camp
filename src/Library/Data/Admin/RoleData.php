<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\UniqueRole;
use App\Model\Entity\Permission;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
#[UniqueRole]
class RoleData implements RoleDataInterface
{
    private ?UuidV4 $id = null;

    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @var Permission[]
     */
    private iterable $permissions = [];

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

    public function getPermissions(): iterable
    {
        return $this->permissions;
    }

    public function setPermissions(iterable $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }
}