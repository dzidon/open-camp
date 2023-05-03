<?php

namespace App\Form\DTO;

use App\Form\Type\User\LoginType;

/**
 * See {@link LoginType}
 */
class UserLoginDTO
{
    public string $email = '';

    public string $password = '';

    public bool $rememberMe = false;
}