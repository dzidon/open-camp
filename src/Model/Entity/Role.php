<?php

namespace App\Model\Entity;

use App\Model\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Admin role used for authorization.
 */
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $label;

    #[ORM\ManyToMany(targetEntity: Permission::class)]
    private Collection $permissions;

    public function __construct(string $label)
    {
        $this->label = $label;
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions->toArray();
    }

    /**
     * Returns permissions grouped by permission group names.
     *
     * @param bool $groupByLabel
     * @return array
     */
    public function getPermissionsGrouped(bool $groupByLabel = false): array
    {
        $permissionsGrouped = [];

        /** @var Permission $permission */
        foreach ($this->permissions as $permission)
        {
            $permissionGroup = $permission->getPermissionGroup();

            if ($groupByLabel)
            {
                $permissionsGrouped[$permissionGroup->getLabel()][] = $permission;
            }
            else
            {
                $permissionsGrouped[$permissionGroup->getName()][] = $permission;
            }
        }

        return $permissionsGrouped;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission))
        {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }
}