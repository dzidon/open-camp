<?php

namespace App\Form\DataTransfer\Data\Admin;

/**
 * Admin role edit data.
 */
interface RoleDataInterface
{
    public function getLabel(): string;

    public function setLabel(?string $label): self;

    public function getPermissions(): iterable;

    public function setPermissions(iterable $permissions): self;
}