<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Entity\Role;

/**
 * Admin user edit data.
 */
interface UserDataInterface
{
    public function getId(): ?int;

    public function setId(?int $id): self;

    public function getEmail(): string;

    public function setEmail(?string $email): self;

    public function getRole(): ?Role;

    public function setRole(?Role $role): self;
}