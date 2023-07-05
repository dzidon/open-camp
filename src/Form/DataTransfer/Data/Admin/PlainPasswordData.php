<?php

namespace App\Form\DataTransfer\Data\Admin;

use App\Form\Type\Admin\RepeatedPasswordType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link RepeatedPasswordType}
 */
class PlainPasswordData implements PlainPasswordDataInterface
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