<?php

namespace App\Model\Enum\Entity;

/**
 * States of the UserRegistration entity.
 */
enum UserRegistrationStateEnum: string
{
    case USED = 'used';
    case UNUSED = 'unused';
    case DISABLED = 'disabled';
}
