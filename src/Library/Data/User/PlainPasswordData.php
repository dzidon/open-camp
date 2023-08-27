<?php

namespace App\Library\Data\User;

use Symfony\Component\Validator\Constraints as Assert;

class PlainPasswordData
{
    #[Assert\Length(min: 6, max: 4096)]
    #[Assert\NotBlank]
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