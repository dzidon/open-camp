<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Model\Entity\Permission;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraint as CustomAssert;

/**
 * @inheritDoc
 */
#[CustomAssert\UniqueRole]
class RoleData implements RoleDataInterface
{
    private ?int $id = null;

    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $label = null;

    /**
     * @var Permission[]
     */
    private iterable $permissions = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
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