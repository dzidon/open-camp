<?php

namespace App\Enum\Entity;

/**
 * States of the user registration entity.
 */
enum UserRegistrationStateEnum: string
{
    case USED = 'used';
    case UNUSED = 'unused';
    case DISABLED = 'disabled';
}
