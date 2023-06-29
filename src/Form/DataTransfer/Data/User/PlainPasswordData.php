<?php

namespace App\Form\DataTransfer\Data\User;

use App\Form\Type\User\RepeatedPasswordType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link RepeatedPasswordType}
 */
class PlainPasswordData implements PlainPasswordDataInterface
{
    #[Assert\Length(min: 6, max: 4096)]
    #[Assert\NotBlank]
    private string $plainPassword = '';

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = (string) $plainPassword;

        return $this;
    }
}