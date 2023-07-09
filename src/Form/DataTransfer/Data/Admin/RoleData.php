<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Form\Type\Admin\RoleType;
use App\Model\Entity\Permission;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link RoleType}
 */
class RoleData implements RoleDataInterface
{
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private string $label = '';

    /**
     * @var Permission[]
     */
    private iterable $permissions = [];

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = (string) $label;

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