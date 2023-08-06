<?php

namespace App\Library\Data\Admin;

/**
 * Admin user plain password edit data.
 */
interface PlainPasswordDataInterface
{
    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): self;
}