<?php

namespace App\Form\DTO\User;

use App\Form\Type\User\LoginType;

/**
 * See {@link LoginType}
 */
class LoginDTO
{
    public ?string $email = null;

    public ?string $password = null;

    public bool $rememberMe = false;
}