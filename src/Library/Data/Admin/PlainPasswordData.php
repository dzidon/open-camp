<?php

namespace App\Library\Data\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class PlainPasswordData
{
    #[Assert\Length(min: 6, max: 4096)]
    private ?string $plainPassword = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}