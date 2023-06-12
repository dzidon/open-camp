<?php

namespace App\Form\DTO\User;

use App\Form\Type\User\PlainPasswordType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link PlainPasswordType}
 */
class PlainPasswordDTO
{
    #[Assert\Length(min: 6, max: 4096)]
    #[Assert\NotBlank]
    public ?string $plainPassword = null;
}