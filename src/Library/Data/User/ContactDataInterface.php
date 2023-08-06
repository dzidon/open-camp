<?php

namespace App\Library\Data\User;

use App\Model\Enum\Entity\ContactRoleEnum;
use libphonenumber\PhoneNumber;

/**
 * User contact edit data.
 */
interface ContactDataInterface
{
    public function getNameFirst(): ?string;

    public function setNameFirst(?string $nameFirst): self;

    public function getNameLast(): ?string;

    public function setNameLast(?string $nameLast): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getPhoneNumber(): ?PhoneNumber;

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self;

    public function getRole(): ?ContactRoleEnum;

    public function setRole(?ContactRoleEnum $role): self;
}