<?php

namespace App\Form\DataTransfer\Data\User;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class PlainPasswordData implements PlainPasswordDataInterface
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