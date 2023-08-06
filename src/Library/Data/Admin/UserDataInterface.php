<?php

namespace App\Library\Data\Admin;

use App\Library\Data\User\BillingDataInterface;
use App\Model\Entity\Role;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin user edit data.
 */
interface UserDataInterface
{
    public function getId(): ?UuidV4;

    public function setId(?UuidV4 $id): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getRole(): ?Role;

    public function setRole(?Role $role): self;

    public function getBillingData(): BillingDataInterface;
}