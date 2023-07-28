<?php

namespace App\Form\DataTransfer\Data\Admin;

use Symfony\Component\Uid\UuidV4;

/**
 * Admin role edit data.
 */
interface RoleDataInterface
{
    public function getId(): ?UuidV4;

    public function setId(?UuidV4 $id): self;

    public function getLabel(): ?string;

    public function setLabel(?string $label): self;

    public function getPermissions(): iterable;

    public function setPermissions(iterable $permissions): self;
}