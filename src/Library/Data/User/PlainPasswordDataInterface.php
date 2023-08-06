<?php

namespace App\Library\Data\User;

/**
 * User plain password edit data.
 */
interface PlainPasswordDataInterface
{
    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): self;
}