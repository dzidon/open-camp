<?php

namespace App\Form\DataTransfer\Data\User;

use libphonenumber\PhoneNumber;

/**
 * User contact edit data.
 */
interface ContactDataInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getPhoneNumber(): ?PhoneNumber;

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self;
}