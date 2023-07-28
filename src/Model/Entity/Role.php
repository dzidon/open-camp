<?php

namespace App\Model\Entity;

use App\Model\Attribute\UpdatedAtProperty;
use App\Model\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin role used for authorization.
 */
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV4 $id;

    #[ORM\Column(length: 64, unique: true)]
    private string $label;

    #[ORM\ManyToMany(targetEntity: Permission::class)]
    private Collection $permissions;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[UpdatedAtProperty(dateTimeType: DateTimeImmutable::class)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $label)
    {
        $this->id = Uuid::v4();
        $this->label = $label;
        $this->permissions = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): UuidV4
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}