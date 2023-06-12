<?php

namespace App\Form\DTO\User;

use App\Form\Type\User\RegistrationPasswordType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link RegistrationPasswordType}
 */
class RegistrationPasswordDTO
{
    #[Assert\Length(min: 6, max: 4096)]
    #[Assert\NotBlank]
    public ?string $plainPassword = null;
}